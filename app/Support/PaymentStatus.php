<?php

namespace App\Support;

/** Label dan warna status pembayaran (kolom payments.status). */
class PaymentStatus
{
    public const LABELS = [
        'pending'    => 'Menunggu pembayaran',
        'settlement' => 'Berhasil',
        'expire'     => 'Kedaluwarsa',
        'cancel'     => 'Dibatalkan',
        'deny'       => 'Ditolak',
        'failed'     => 'Gagal',
    ];

    public const BADGES = [
        'pending'    => 'bg-yellow-100 text-yellow-800',
        'settlement' => 'bg-green-100 text-green-800',
        'expire'     => 'bg-gray-100 text-gray-700',
        'cancel'     => 'bg-gray-100 text-gray-700',
        'deny'       => 'bg-red-100 text-red-800',
        'failed'     => 'bg-red-100 text-red-800',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? ucfirst($status);
    }

    public static function badge(string $status): string
    {
        return self::BADGES[$status] ?? 'bg-gray-100 text-gray-800';
    }
}
