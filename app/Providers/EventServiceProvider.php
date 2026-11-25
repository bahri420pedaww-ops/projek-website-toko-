<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Listeners\SendOrderPaidNotifications;
use App\Listeners\SendOrderPlacedNotifications;
use App\Listeners\SendOrderShippedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Pemetaan event ke listener.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Email notifikasi toko
        OrderPlaced::class => [
            SendOrderPlacedNotifications::class,
        ],
        OrderPaid::class => [
            SendOrderPaidNotifications::class,
        ],
        OrderShipped::class => [
            SendOrderShippedNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
