<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $counts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', [
            'toProcess' => (int) ($counts[Order::PAID] ?? 0),
            'toShip'    => (int) ($counts[Order::PROCESSING] ?? 0),
            'unpaid'    => (int) ($counts[Order::PENDING] ?? 0),
            'shipped'   => (int) ($counts[Order::SHIPPED] ?? 0),
        ]);
    }
}
