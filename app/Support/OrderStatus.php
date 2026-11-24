<?php

namespace App\Support;

/**
 * Label dan warna status pesanan, supaya tampilan di semua halaman seragam.
 */
class OrderStatus
{
    public const LABELS = [
        'pending'    => 'Menunggu pembayaran',
        'paid'       => 'Sudah dibayar',
        'processing' => 'Diproses',
        'shipped'    => 'Dikirim',
        'completed'  => 'Selesai',
        'cancelled'  => 'Dibatalkan',
    ];

    public const BADGES = [
        'pending'    => 'bg-yellow-100 text-yellow-800',
        'paid'       => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'shipped'    => 'bg-purple-100 text-purple-800',
        'completed'  => 'bg-green-100 text-green-800',
        'cancelled'  => 'bg-red-100 text-red-800',
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
