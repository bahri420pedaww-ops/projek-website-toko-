<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin melihat data pelanggan (pembeli) beserta alamat mereka. Hanya baca (read-only).
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

        return view('admin.customers.show', compact('customer', 'addresses'));
    }
}
