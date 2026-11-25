<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Mengirim satu notifikasi ke admin toko.
 * SHOP_ADMIN_EMAIL (di .env) diutamakan; kalau kosong, dikirim ke semua akun berperan admin.
 */
class AdminRecipients
{
    public static function notify(Notification $notification): void
    {
        $email = config('shop.admin_notification_email');

        if ($email) {
            NotificationFacade::route('mail', $email)->notify($notification);

            return;
        }

        $admins = User::where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            NotificationFacade::send($admins, $notification);
        }
    }
}
