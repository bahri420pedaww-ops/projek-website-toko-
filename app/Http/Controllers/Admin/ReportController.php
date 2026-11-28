<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    private const MAX_DAYS = 366;

    public function __construct(private ReportService $reports)
    {
    }

    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        return view('admin.reports.index', [
            'from'    => $from,
            'to'      => $to,
            'report'  => $this->reports->build($from, $to),
            'presets' => $this->presets(),
            'lowStockThreshold' => ReportService::LOW_STOCK_THRESHOLD,
        ]);
    }

    /** Unduh CSV daftar pesanan yang lunas pada rentang yang dipilih (dibuka di Excel). */
    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);

        $query = $this->reports->exportQuery($from, $to);
        $filename = 'laporan-penjualan-' . $from->format('Ymd') . '-sd-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // BOM supaya Excel membaca huruf UTF-8 dengan benar; pemisah titik koma
            // karena Excel berbahasa Indonesia memakai ; sebagai pemisah kolom.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Kode Pesanan', 'Tanggal Bayar', 'Metode Bayar', 'Pembeli', 'Email Pembeli',
                'Penerima', 'Kota', 'Provinsi', 'Jumlah Barang',
                'Subtotal', 'Ongkos Kirim', 'Diskon', 'Total', 'Kurir', 'Status Pesanan',
            ], ';');

            foreach ($query->cursor() as $row) {
                fputcsv($out, [
                    $this->safe($row->order_code),
                    $row->paid_at,
                    $this->safe($row->payment_type),
                    $this->safe($row->buyer_name),
                    $this->safe($row->buyer_email),
                    $this->safe($row->receiver_name),
                    $this->safe($row->city_name),
                    $this->safe($row->province_name),
                    (int) $row->items_qty,
                    (int) $row->subtotal,
                    (int) $row->shipping_cost,
                    (int) $row->discount_total,
                    (int) $row->grand_total,
                    $this->safe(trim(($row->courier ?? '') . ' ' . ($row->service ?? ''))),
                    $this->safe($row->status),
                ], ';');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Rentang tanggal dari ?from dan ?to. Bawaan: 30 hari terakhir sampai hari ini. */
    private function range(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ], [
            'from.date'          => 'Tanggal awal tidak valid.',
            'to.date'            => 'Tanggal akhir tidak valid.',
            'to.after_or_equal'  => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $to = ! empty($data['to'])
            ? Carbon::parse($data['to'])->endOfDay()
            : now()->endOfDay();

        $from = ! empty($data['from'])
            ? Carbon::parse($data['from'])->startOfDay()
            : $to->copy()->subDays(29)->startOfDay();

        if ($from->diffInDays($to) > self::MAX_DAYS) {
            throw ValidationException::withMessages([
                'to' => 'Rentang laporan maksimal 1 tahun.',
            ]);
        }

        return [$from, $to];
    }

    /** Pintasan rentang tanggal yang sering dipakai. */
    private function presets(): array
    {
        $today = now();

        return [
            'Hari ini'   => [$today->format('Y-m-d'), $today->format('Y-m-d')],
            '7 hari'     => [$today->copy()->subDays(6)->format('Y-m-d'), $today->format('Y-m-d')],
            '30 hari'    => [$today->copy()->subDays(29)->format('Y-m-d'), $today->format('Y-m-d')],
            'Bulan ini'  => [$today->copy()->startOfMonth()->format('Y-m-d'), $today->format('Y-m-d')],
            'Bulan lalu' => [
                $today->copy()->subMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
                $today->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d'),
            ],
        ];
    }

    /**
     * Cegah "CSV injection": sel yang diawali = + - @ bisa dijalankan sebagai rumus di Excel.
     * Awalan tanda petik membuatnya dibaca sebagai teks biasa.
     */
    private function safe(?string $value): string
    {
        $value = (string) $value;

        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
