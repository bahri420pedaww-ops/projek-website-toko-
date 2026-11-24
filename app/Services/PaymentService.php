<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Exceptions\MidtransException;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Alur pembayaran Midtrans (Snap):
 *   1. startPayment()        membuat token Snap untuk satu pesanan
 *   2. handleNotification()  menerima webhook dari Midtrans (diverifikasi dengan signature)
 *   3. syncLatest()          menanyakan status ke Midtrans langsung (tombol "Cek status")
 * Webhook dan syncLatest memakai applyStatus() yang sama, jadi hasilnya selalu konsisten.
 */
class PaymentService
{
    public function __construct(
        private MidtransClient $midtrans,
        private OrderService $orders,
    ) {
    }

    /**
     * Siapkan pembayaran untuk pesanan. Token yang masih berlaku dipakai ulang,
     * jadi membuka halaman bayar berkali-kali tidak membuat transaksi ganda.
     *
     * @throws DomainException|MidtransException
     */
    public function startPayment(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {
            // Kunci pesanan agar dua klik "Bayar" tidak membuat dua transaksi
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== Order::PENDING) {
                throw new DomainException('Pesanan ini tidak sedang menunggu pembayaran.');
            }

            $latest = $order->payments()->latest('id')->first();
            $freshLimit = now()->subHours((int) config('midtrans.expiry_hours'))->addMinutes(5);

            if ($latest && $latest->status === 'pending' && $latest->snap_token && $latest->created_at->gt($freshLimit)) {
                return $latest;
            }

            // Percobaan pertama memakai kode pesanan sebagai order_id Midtrans.
            // Percobaan berikutnya diberi akhiran -2, -3, ... karena order_id harus unik.
            $attempt = $order->payments()->count() + 1;
            $gatewayOrderId = $attempt === 1 ? $order->order_code : $order->order_code . '-' . $attempt;

            $result = $this->midtrans->createSnapTransaction($this->buildPayload($order, $gatewayOrderId));

            return $order->payments()->create([
                'gateway_order_id' => $gatewayOrderId,
                'gross_amount'     => (int) $order->grand_total,
                'status'           => 'pending',
                'snap_token'       => $result['token'],
            ]);
        });
    }

    /**
     * Proses notifikasi (webhook) dari Midtrans.
     *
     * @throws InvalidArgumentException jika data tidak lengkap atau signature tidak cocok
     * @throws MidtransException        jika Server Key belum diatur
     */
    public function handleNotification(array $payload): ?Payment
    {
        $serverKey = (string) config('midtrans.server_key');

        if ($serverKey === '') {
            throw new MidtransException('MIDTRANS_SERVER_KEY belum diatur di file .env.');
        }

        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $field) {
            if (! isset($payload[$field]) || $payload[$field] === '') {
                throw new InvalidArgumentException("Field {$field} tidak ada pada notifikasi.");
            }
        }

        // SHA512(order_id + status_code + gross_amount + ServerKey); gross_amount dipakai persis
        // seperti string yang dikirim Midtrans (misalnya "150000.00").
        $expected = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        if (! hash_equals($expected, (string) $payload['signature_key'])) {
            throw new InvalidArgumentException('Signature notifikasi tidak valid.');
        }

        $payment = Payment::where('gateway_order_id', $payload['order_id'])->first();

        if (! $payment) {
            // Midtrans sendiri bisa mengirim notifikasi uji dengan order_id yang tidak kita kenal
            Log::warning('Notifikasi Midtrans untuk order_id yang tidak dikenal: ' . $payload['order_id']);

            return null;
        }

        if ((int) round((float) $payload['gross_amount']) !== (int) $payment->gross_amount) {
            Log::error('Nominal notifikasi Midtrans tidak cocok dengan pembayaran ' . $payment->gateway_order_id);

            return null;
        }

        $this->applyStatus($payment, $payload);

        return $payment->refresh();
    }

    /**
     * Tanyakan status pembayaran terakhir pesanan langsung ke Midtrans, lalu terapkan.
     * Berguna di localhost, saat webhook Midtrans tidak bisa menjangkau komputer ini.
     *
     * @throws MidtransException
     */
    public function syncLatest(Order $order): ?Payment
    {
        $payment = $order->payments()->latest('id')->first();

        if (! $payment) {
            return null;
        }

        $data = $this->midtrans->status($payment->gateway_order_id);

        if ($data !== null) {
            $this->applyStatus($payment, $data);
        }

        return $payment->refresh();
    }

    /**
     * Terapkan status dari Midtrans ke pembayaran dan pesanan. Aman dipanggil berulang
     * (idempoten) dan tidak pernah "menurunkan" pembayaran yang sudah berhasil.
     */
    private function applyStatus(Payment $payment, array $data): void
    {
        $transaction = (string) ($data['transaction_status'] ?? '');
        $fraud = $data['fraud_status'] ?? null;
        $newStatus = $this->mapStatus($transaction, $fraud);

        // Sukses harus berstatus kode 200 (tiga syarat: status_code, fraud_status, transaction_status)
        if ($newStatus === 'settlement' && (string) ($data['status_code'] ?? '') !== '200') {
            $newStatus = null;
        }

        if ($newStatus === null) {
            return; // status yang tidak dipakai (refund dan sejenisnya) diabaikan
        }

        $paidOrderId = null;

        DB::transaction(function () use ($payment, $data, $newStatus, &$paidOrderId) {
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();

            // Pembayaran yang sudah berhasil tidak boleh berubah lagi (notifikasi bisa datang terlambat/ganda)
            if ($payment->status === 'settlement') {
                return;
            }

            // Status akhir tidak boleh kembali ke "pending" oleh notifikasi yang terlambat
            if ($newStatus === 'pending' && in_array($payment->status, ['expire', 'cancel'], true)) {
                return;
            }

            $payment->transaction_id = $data['transaction_id'] ?? $payment->transaction_id;
            $payment->payment_type = $data['payment_type'] ?? $payment->payment_type;
            $payment->status = $newStatus;

            if ($newStatus === 'settlement') {
                $payment->paid_at = $this->parseTime($data['settlement_time'] ?? null) ?? now();
            }

            $payment->save();

            if ($newStatus === 'settlement') {
                if ($order->status === Order::PENDING) {
                    $this->orders->markPaid($order, 'Pembayaran diterima melalui Midtrans.');
                    $paidOrderId = $order->id;
                } else {
                    // Kasus langka: uang masuk setelah pesanan dibatalkan. Butuh penanganan manual.
                    Log::warning("Pembayaran {$payment->gateway_order_id} berhasil, tetapi pesanan berstatus {$order->status}.");
                    $order->histories()->create([
                        'status' => $order->status,
                        'note'   => 'Pembayaran diterima setelah pesanan berstatus ' . $order->status
                                  . '. Perlu ditangani manual oleh admin.',
                    ]);
                }
            } elseif (in_array($newStatus, ['expire', 'cancel'], true)) {
                // Batalkan pesanan hanya kalau ini percobaan pembayaran TERAKHIR
                $latestId = (int) Payment::where('order_id', $order->id)->max('id');

                if ($order->status === Order::PENDING && $payment->id === $latestId) {
                    $this->orders->cancel(
                        $order,
                        $newStatus === 'expire' ? 'Pembayaran kedaluwarsa.' : 'Pembayaran dibatalkan.'
                    );
                }
            }
            // 'deny' dan 'failed': pesanan tetap menunggu pembayaran; pembeli bisa mencoba lagi
        });

        // Event dipicu SETELAH transaksi database selesai
        if ($paidOrderId) {
            event(new OrderPaid(Order::findOrFail($paidOrderId)));
        }
    }

    /** Terjemahkan status Midtrans ke status pada tabel payments. null = diabaikan. */
    private function mapStatus(string $transaction, ?string $fraud): ?string
    {
        return match ($transaction) {
            'capture'            => match ($fraud) {
                'challenge' => 'pending',
                'deny'      => 'deny',
                default     => 'settlement', // accept atau null
            },
            'settlement'         => 'settlement',
            'pending', 'authorize' => 'pending',
            'deny'               => 'deny',
            'cancel'             => 'cancel',
            'expire'             => 'expire',
            'failure'            => 'failed',
            default              => null,
        };
    }

    /** Waktu dari Midtrans berzona WIB; ubah ke zona aplikasi. */
    private function parseTime(?string $time): ?Carbon
    {
        if (! $time) {
            return null;
        }

        try {
            return Carbon::parse($time, 'Asia/Jakarta')->setTimezone(config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }

    private function buildPayload(Order $order, string $gatewayOrderId): array
    {
        $order->loadMissing(['items', 'user']);

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id'       => 'P' . $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => Str::limit($item->product_name, 50, ''),
            ];
        }

        if ($order->shipping_cost > 0) {
            $items[] = ['id' => 'SHIPPING', 'price' => (int) $order->shipping_cost, 'quantity' => 1, 'name' => 'Ongkos kirim'];
        }

        if ($order->discount_total > 0) {
            $items[] = ['id' => 'DISCOUNT', 'price' => -(int) $order->discount_total, 'quantity' => 1, 'name' => 'Diskon'];
        }

        $itemsTotal = collect($items)->sum(fn ($i) => $i['price'] * $i['quantity']);

        $payload = [
            'transaction_details' => [
                'order_id'     => $gatewayOrderId,
                'gross_amount' => (int) $order->grand_total,
            ],
            'customer_details' => [
                'first_name'       => Str::limit($order->user->name, 100, ''),
                'email'            => $order->user->email,
                'phone'            => $order->receiver_phone,
                'shipping_address' => array_filter([
                    'first_name'   => Str::limit($order->receiver_name, 100, ''),
                    'phone'        => $order->receiver_phone,
                    'address'      => Str::limit($order->shipping_address, 200, ''),
                    'city'         => Str::limit((string) $order->city_name, 50, ''),
                    'postal_code'  => $order->postal_code,
                    'country_code' => 'IDN',
                ]),
            ],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => (int) config('midtrans.expiry_hours'),
            ],
        ];

        // Midtrans menolak transaksi bila jumlah item tidak sama dengan gross_amount
        if ($itemsTotal === (int) $order->grand_total) {
            $payload['item_details'] = $items;
        }

        return $payload;
    }
}
