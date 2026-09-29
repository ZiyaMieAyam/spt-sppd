<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin development. Kredensial default hanya fallback lokal;
        // set SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD di environment untuk
        // menimpa tanpa mengubah kode (mis. staging/production).
        User::firstOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@gmail.com')],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
            ]
        );

        $this->call([
            PegawaiSeeder::class,
            KecamatanSeeder::class,
            KotaTujuanSeeder::class,
            UserSeeder::class,
            PenandatanganSeeder::class,
        ]);
    }
}
