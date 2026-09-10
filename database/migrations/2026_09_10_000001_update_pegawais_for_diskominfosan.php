<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah enum PPPK dan buat pangkat nullable agar PPPK bisa NULL (tidak mengarang pangkat)
        DB::statement("ALTER TABLE `pegawais` MODIFY COLUMN `status` ENUM('ASN','Non ASN','PPPK') NOT NULL DEFAULT 'ASN'");
        DB::statement('ALTER TABLE `pegawais` MODIFY COLUMN `pangkat` VARCHAR(255) NULL');
    }

    public function down(): void
    {
        // Kembalikan ke semula (hilang PPPK, pangkat NOT NULL). Data PPPK akan fallback ke ASN
        // Set pangkat NULL menjadi '-' agar tidak melanggar NOT NULL saat rollback
        DB::table('pegawais')->whereNull('pangkat')->update(['pangkat' => '-']);
        // Jika ada PPPK, ubah ke ASN sebelum revert enum
        DB::table('pegawais')->where('status', 'PPPK')->update(['status' => 'ASN']);
        DB::statement("ALTER TABLE `pegawais` MODIFY COLUMN `status` ENUM('ASN','Non ASN') NOT NULL DEFAULT 'ASN'");
        DB::statement('ALTER TABLE `pegawais` MODIFY COLUMN `pangkat` VARCHAR(255) NOT NULL');
    }
};
