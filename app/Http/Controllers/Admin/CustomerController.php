<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin melihat data pelanggan (pembeli) beserta alamat dan pesanan mereka. Hanya baca (read-only).
 */
class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('q'));

        $customers = User::query()
            ->where('role', 'customer')
            ->withCount('addresses')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($w) use ($keyword) {
                    $w->where('name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%")
                      ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'keyword'));
    }

    public function show(User $customer)
    {
        // Halaman ini khusus pelanggan; akun admin tidak ditampilkan
        abort_unless($customer->role === 'customer', 404);

        $addresses = $customer->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        $orders = Order::where('user_id', $customer->id)
            ->latest('id')
            ->limit(10)
            ->get();

        // Total belanja hanya dari pesanan yang sudah dibayar sampai selesai
        $totalSpent = (int) Order::where('user_id', $customer->id)
            ->whereIn('status', [Order::PAID, Order::PROCESSING, Order::SHIPPED, Order::COMPLETED])
            ->sum('grand_total');

        return view('admin.customers.show', compact('customer', 'addresses', 'orders', 'totalSpent'));
    }
}
