<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah enum PPPK dan buat pangkat nullable agar PPPK bisa NULL (tidak mengarang pangkat)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `pegawais` MODIFY COLUMN `status` ENUM('ASN','Non ASN','PPPK') NOT NULL DEFAULT 'ASN'");
            DB::statement('ALTER TABLE `pegawais` MODIFY COLUMN `pangkat` VARCHAR(255) NULL');

            return;
        }

        // SQLite (test) tidak mendukung MODIFY COLUMN: rebuild tabel
        // dengan skema setara (status menerima PPPK, pangkat nullable).
        // Kolom/relasi/index lain tidak diubah.
        Schema::disableForeignKeyConstraints();

        Schema::create('pegawais_baru', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('pangkat')->nullable();
            $table->string('golongan');
            $table->string('jabatan');
            $table->enum('kode_sppd', [
                '097.2',
                '097.3',
                '097.4',
                '097.5',
            ]);
            $table->string('unit_kerja');
            $table->enum('status', [
                'ASN',
                'Non ASN',
                'PPPK',
            ])->default('ASN');
            $table->timestamps();
        });

        $kolom = ['id', 'nip', 'nama', 'pangkat', 'golongan', 'jabatan', 'kode_sppd', 'unit_kerja', 'status', 'created_at', 'updated_at'];
        DB::table('pegawais_baru')->insertUsing($kolom, DB::table('pegawais')->select($kolom));

        Schema::drop('pegawais');
        Schema::rename('pegawais_baru', 'pegawais');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Kembalikan ke semula (hilang PPPK, pangkat NOT NULL). Data PPPK akan fallback ke ASN
        // Set pangkat NULL menjadi '-' agar tidak melanggar NOT NULL saat rollback
        DB::table('pegawais')->whereNull('pangkat')->update(['pangkat' => '-']);
        // Jika ada PPPK, ubah ke ASN sebelum revert enum
        DB::table('pegawais')->where('status', 'PPPK')->update(['status' => 'ASN']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `pegawais` MODIFY COLUMN `status` ENUM('ASN','Non ASN') NOT NULL DEFAULT 'ASN'");
            DB::statement('ALTER TABLE `pegawais` MODIFY COLUMN `pangkat` VARCHAR(255) NOT NULL');

            return;
        }

        // SQLite (test): rebuild kembali ke skema semula.
        Schema::disableForeignKeyConstraints();

        Schema::create('pegawais_lama', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('pangkat');
            $table->string('golongan');
            $table->string('jabatan');
            $table->enum('kode_sppd', [
                '097.2',
                '097.3',
                '097.4',
                '097.5',
            ]);
            $table->string('unit_kerja');
            $table->enum('status', [
                'ASN',
                'Non ASN',
            ])->default('ASN');
            $table->timestamps();
        });

        $kolom = ['id', 'nip', 'nama', 'pangkat', 'golongan', 'jabatan', 'kode_sppd', 'unit_kerja', 'status', 'created_at', 'updated_at'];
        DB::table('pegawais_lama')->insertUsing($kolom, DB::table('pegawais')->select($kolom));

        Schema::drop('pegawais');
        Schema::rename('pegawais_lama', 'pegawais');

        Schema::enableForeignKeyConstraints();
    }
};
