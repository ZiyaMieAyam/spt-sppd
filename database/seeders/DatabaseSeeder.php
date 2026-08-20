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
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        $this->call([
            PegawaiSeeder::class,
            KecamatanSeeder::class,
            KotaTujuanSeeder::class,
            UserSeeder::class,
        ]);
    }
}