<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::whereIn('status', [
                Order::PAID,
                Order::PROCESSING,
                Order::SHIPPED,
                Order::COMPLETED,
            ])->sum('grand_total'),
            'latestOrders' => Order::with('user')->latest()->take(5)->get(),
        ]);
    }
}
