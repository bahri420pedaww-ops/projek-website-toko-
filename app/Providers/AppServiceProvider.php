<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.navbar', function ($view) {
            $cart = session('cart', []);
            $cartCount = is_array($cart)
                ? array_sum(array_map(function ($quantity) {
                    return max(0, (int) $quantity);
                }, $cart))
                : 0;
            $orderCount = auth()->check() && auth()->user()->role === 'customer'
                ? Order::where('user_id', auth()->id())->count()
                : 0;

            $view->with([
                'cartCount' => $cartCount,
                'orderCount' => $orderCount,
            ]);
        });
    }
}
