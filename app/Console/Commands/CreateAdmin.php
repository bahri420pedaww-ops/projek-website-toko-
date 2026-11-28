<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Membuat akun admin dengan password pilihanmu sendiri (tidak lewat seeder).
 *
 *   php artisan shop:create-admin email@anda.com --name="Nama Admin"
 */
class CreateAdmin extends Command
{
    protected $signature = 'shop:create-admin {email : Email admin} {--name=Admin Toko : Nama admin}';

    protected $description = 'Buat akun admin baru dengan password pilihanmu sendiri';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $name = trim((string) $this->option('name'));

        $emailCheck = Validator::make(['email' => $email], ['email' => ['required', 'email']]);

        if ($emailCheck->fails()) {
            $this->error('Format email tidak valid.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Email {$email} sudah terdaftar. Pakai email lain.");

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password (minimal 8 karakter)');
        $confirm = (string) $this->secret('Ulangi password');

        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        if ($password !== $confirm) {
            $this->error('Konfirmasi password tidak sama.');

            return self::FAILURE;
        }

        $user = new User();
        $user->forceFill([
            'name'              => $name !== '' ? $name : 'Admin Toko',
            'email'             => $email,
            'role'              => 'admin',
            'password'          => Hash::make($password),
            'email_verified_at' => now(),
        ])->save();

        $this->info("Admin dibuat: {$email}. Masuk lewat /login lalu buka /admin.");

        return self::SUCCESS;
    }
}
