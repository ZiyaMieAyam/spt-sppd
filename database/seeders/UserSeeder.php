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
                'password' => '12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kepegawaian@diskominfo.balangankab.go.id'],
            [
                'name' => 'Bidang Kepegawaian',
                'password' => 'pegawai12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'sekretariat@diskominfo.balangankab.go.id'],
            [
                'name' => 'Sekretariat',
                'password' => 'sekretariat12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'perencanaan@diskominfo.balangankab.go.id'],
            [
                'name' => 'Bidang Perencanaan',
                'password' => 'perencanaan12345',
            ]
        );

        User::updateOrCreate(
            ['email' => 'diskominfosan@balangankab.go.id'],
            [
                'name' => 'Diskominfosan',
                'password' => 'diskominfo12345',
            ]
        );
    }
}