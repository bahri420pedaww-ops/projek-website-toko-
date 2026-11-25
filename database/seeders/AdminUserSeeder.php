<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@styleshop.com'],
            [
                'name' => 'StyleShop Admin',
                'phone' => '080000000000',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
