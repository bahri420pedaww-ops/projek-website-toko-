<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Services\AddressService;
use DomainException;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(private AddressService $addresses)
    {
    }

    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        return view('account.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('account.addresses.create');
    }

    public function store(AddressRequest $request)
    {
        $data = $request->safe()->except('is_default');

        try {
            $this->addresses->create($request->user(), $data, $request->boolean('is_default'));
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Request $request, int $address)
    {
        // Hanya alamat milik user yang login; selain itu 404
        $address = $request->user()->addresses()->findOrFail($address);

        return view('account.addresses.edit', compact('address'));
    }

    public function update(AddressRequest $request, int $address)
    {
        $address = $request->user()->addresses()->findOrFail($address);
        $data = $request->safe()->except('is_default');

        $this->addresses->update($request->user(), $address, $data, $request->boolean('is_default'));

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function setDefault(Request $request, int $address)
    {
        $address = $request->user()->addresses()->findOrFail($address);

        $this->addresses->makeDefault($request->user(), $address);

        return back()->with('success', 'Alamat utama diperbarui.');
    }

    public function destroy(Request $request, int $address)
    {
        $address = $request->user()->addresses()->findOrFail($address);

        $this->addresses->delete($request->user(), $address);

        return back()->with('success', 'Alamat berhasil dihapus.');
    }
}
