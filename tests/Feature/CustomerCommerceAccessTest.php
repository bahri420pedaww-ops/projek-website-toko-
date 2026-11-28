<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CustomerCommerceAccessTest extends TestCase
{
    public function test_admin_is_redirected_away_from_cart_and_checkout(): void
    {
        $admin = new User(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('cart.index'))
            ->assertRedirect(route('admin.dashboard'));

        $this->get(route('checkout.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_logout_clears_the_cart_from_the_session(): void
    {
        $customer = new User(['role' => 'customer']);

        $this->actingAs($customer)
            ->withSession(['cart' => [123 => 2]])
            ->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('cart');
    }
}
