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

        User::updateOrCreate(
            ['email' => 'kepegawaian@diskominfo.balangankab.go.id'],
            [
                'name' => 'Bidang Kepegawaian',
                'role' => 'user',
                'password' => 'pegawai12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'sekretariat@diskominfo.balangankab.go.id'],
            [
                'name' => 'Sekretariat',
                'role' => 'user',
                'password' => 'sekretariat12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'perencanaan@diskominfo.balangankab.go.id'],
            [
                'name' => 'Bidang Perencanaan',
                'role' => 'user',
                'password' => 'perencanaan12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'diskominfosan@balangankab.go.id'],
            [
                'name' => 'Diskominfosan',
                'role' => 'user',
                'password' => 'diskominfo12345',
            ]
        );
    }
}
