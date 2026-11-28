<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Laporan penjualan untuk admin.
 *
 * DEFINISI PENTING
 *   "Penjualan" = pesanan yang pembayarannya BERHASIL (payments.status = settlement), dihitung
 *   pada tanggal pembayaran (payments.paid_at). Pesanan yang belum dibayar atau dibatalkan tidak
 *   masuk pendapatan. Karena pesanan yang sudah dibayar tidak bisa dibatalkan, pesanan berstatus
 *   paid, processing, shipped, dan completed semuanya terhitung.
 *   Pendapatan = total yang dibayar pembeli (barang + ongkos kirim - diskon).
 */
class ReportService
{
    public const LOW_STOCK_THRESHOLD = 5;

    public function build(Carbon $from, Carbon $to): array
    {
        $paid = $this->paidOrders($from, $to);

        $totals = (clone $paid)
            ->selectRaw('COUNT(o.id) as orders_count')
            ->selectRaw('COALESCE(SUM(o.grand_total), 0) as revenue')
            ->selectRaw('COALESCE(SUM(o.subtotal), 0) as goods')
            ->selectRaw('COALESCE(SUM(o.shipping_cost), 0) as shipping')
            ->selectRaw('COALESCE(SUM(o.discount_total), 0) as discount')
            ->first();

        $ordersCount = (int) $totals->orders_count;
        $revenue = (int) $totals->revenue;

        $itemsSold = (int) $this->paidItems($from, $to)->sum('oi.quantity');

        return [
            'totals' => [
                'orders'    => $ordersCount,
                'revenue'   => $revenue,
                'goods'     => (int) $totals->goods,
                'shipping'  => (int) $totals->shipping,
                'discount'  => (int) $totals->discount,
                'average'   => $ordersCount > 0 ? (int) round($revenue / $ordersCount) : 0,
                'items'     => $itemsSold,
            ],
            'daily'         => $this->daily($paid, $from, $to),
            'top_products'  => $this->topProducts($from, $to),
            'status_counts' => $this->statusCounts($from, $to),
            'new_customers' => User::where('role', 'customer')->whereBetween('created_at', [$from, $to])->count(),
            'low_stock'     => Product::where('is_active', true)
                                ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                                ->orderBy('stock')
                                ->limit(10)
                                ->get(['id', 'name', 'stock']),
        ];
    }

    /** Baris pesanan yang lunas dalam rentang waktu, untuk ekspor CSV. */
    public function exportQuery(Carbon $from, Carbon $to): Builder
    {
        return $this->paidOrders($from, $to)
            ->join('users as u', 'u.id', '=', 'o.user_id')
            ->leftJoin('shipping_methods as s', 's.id', '=', 'o.shipping_method_id')
            ->select([
                'o.order_code', 'p.paid_at', 'p.payment_type', 'u.name as buyer_name', 'u.email as buyer_email',
                'o.receiver_name', 'o.city_name', 'o.province_name',
                'o.subtotal', 'o.shipping_cost', 'o.discount_total', 'o.grand_total',
                'o.status', 's.courier', 's.service',
            ])
            ->selectRaw('(SELECT COALESCE(SUM(quantity), 0) FROM order_items WHERE order_id = o.id) as items_qty')
            ->orderBy('p.paid_at')
            ->orderBy('o.id');
    }

    /** Pesanan dengan pembayaran berhasil pada rentang waktu (alias tabel: o = orders, p = payments). */
    private function paidOrders(Carbon $from, Carbon $to): Builder
    {
        return DB::table('orders as o')
            ->join('payments as p', function ($join) {
                $join->on('p.order_id', '=', 'o.id')->where('p.status', '=', 'settlement');
            })
            ->whereBetween('p.paid_at', [$from, $to]);
    }

    /** Barang di pesanan yang lunas pada rentang waktu (alias: oi = order_items). */
    private function paidItems(Carbon $from, Carbon $to): Builder
    {
        return DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->join('payments as p', function ($join) {
                $join->on('p.order_id', '=', 'o.id')->where('p.status', '=', 'settlement');
            })
            ->whereBetween('p.paid_at', [$from, $to]);
    }

    /** Penjualan per hari; hari tanpa penjualan tetap ditampilkan dengan nilai 0. */
    private function daily(Builder $paid, Carbon $from, Carbon $to): array
    {
        $rows = (clone $paid)
            ->selectRaw('DATE(p.paid_at) as day')
            ->selectRaw('COUNT(o.id) as orders_count')
            ->selectRaw('SUM(o.grand_total) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $days = [];
        $max = 0;

        foreach (CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()) as $date) {
            $key = $date->format('Y-m-d');
            $row = $rows->get($key);
            $revenue = $row ? (int) $row->revenue : 0;

            $days[] = [
                'date'    => $date->copy(),
                'orders'  => $row ? (int) $row->orders_count : 0,
                'revenue' => $revenue,
            ];

            $max = max($max, $revenue);
        }

        // Persentase untuk lebar batang di tampilan
        foreach ($days as &$day) {
            $day['percent'] = $max > 0 ? (int) round($day['revenue'] / $max * 100) : 0;
        }
        unset($day);

        return $days;
    }

    /** 10 produk terlaris berdasarkan jumlah terjual. */
    private function topProducts(Carbon $from, Carbon $to): array
    {
        return $this->paidItems($from, $to)
            ->selectRaw('oi.product_id')
            ->selectRaw('MAX(oi.product_name) as name')
            ->selectRaw('SUM(oi.quantity) as qty')
            ->selectRaw('SUM(oi.price * oi.quantity) as revenue')
            ->groupBy('oi.product_id')
            ->orderByDesc('qty')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'name'    => $row->name,
                'qty'     => (int) $row->qty,
                'revenue' => (int) $row->revenue,
            ])
            ->all();
    }

    /** Jumlah pesanan yang DIBUAT pada rentang waktu, dikelompokkan per status saat ini. */
    private function statusCounts(Carbon $from, Carbon $to): array
    {
        $counts = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $result = [];

        foreach (array_keys(\App\Support\OrderStatus::LABELS) as $status) {
            $result[$status] = (int) ($counts[$status] ?? 0);
        }

        return $result;
    }
}
