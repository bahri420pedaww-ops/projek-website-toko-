<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Logika alamat pembeli. Aturan utama: setiap user selalu punya SATU alamat utama
 * (kalau punya alamat). Langkah Checkout nanti memakai alamat ini.
 */
class AddressService
{
    public const MAX_ADDRESSES = 10;

    /**
     * Alamat pertama otomatis menjadi alamat utama.
     *
     * @throws DomainException
     */
    public function create(User $user, array $data, bool $makeDefault): Address
    {
        return DB::transaction(function () use ($user, $data, $makeDefault) {
            $count = $user->addresses()->count();

            if ($count >= self::MAX_ADDRESSES) {
                throw new DomainException('Maksimal ' . self::MAX_ADDRESSES . ' alamat per akun.');
            }

            $isDefault = $makeDefault || $count === 0;

            if ($isDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            return $user->addresses()->create($data + ['is_default' => $isDefault]);
        });
    }

    /**
     * Ubah alamat. Mencentang "alamat utama" memindahkan status utama ke alamat ini.
     * Alamat utama tidak bisa "dimatikan" dari sini; pilih alamat lain sebagai utama.
     */
    public function update(User $user, Address $address, array $data, bool $makeDefault): Address
    {
        DB::transaction(function () use ($user, $address, $data, $makeDefault) {
            $address->update($data);

            if ($makeDefault && ! $address->is_default) {
                $this->makeDefault($user, $address);
            }
        });

        return $address;
    }

    public function makeDefault(User $user, Address $address): void
    {
        DB::transaction(function () use ($user, $address) {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
    }

    /**
     * Hapus alamat (soft delete). Kalau yang dihapus alamat utama,
     * alamat terbaru yang tersisa otomatis menjadi alamat utama.
     */
    public function delete(User $user, Address $address): void
    {
        DB::transaction(function () use ($user, $address) {
            $wasDefault = $address->is_default;

            $address->update(['is_default' => false]);
            $address->delete();

            if ($wasDefault) {
                $user->addresses()->latest('id')->first()?->update(['is_default' => true]);
            }
        });
    }
}
