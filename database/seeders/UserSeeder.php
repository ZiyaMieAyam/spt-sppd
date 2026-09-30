<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun user development. Hash eksplisit (hasil sama seperti sebelumnya
        // lewat cast 'hashed', yang melewatkan nilai yang sudah di-hash).
        // Kredensial bisa ditimpa via environment tanpa mengubah kode.
        User::updateOrCreate(
            ['email' => env('SEED_USER_EMAIL', 'zia@zia.com')],
            [
                'name' => 'zia',
                'role' => UserRole::User,
                'password' => Hash::make(env('SEED_USER_PASSWORD', '12345')),
            ]
        );
    }
}
