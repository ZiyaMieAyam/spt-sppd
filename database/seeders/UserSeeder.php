<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'zia@zia.com'],
            [
                'name' => 'zia',
                'role' => 'user',
                'password' => '12345',
            ]
        );
    }
}
