<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel settings tidak dipakai aplikasi mana pun
     * (tidak ada model maupun query yang merujuknya),
     * sehingga dihapus. Migration pembuatnya
     * (2026_08_03_074315) tidak diubah agar riwayat
     * database yang sudah menjalankannya tetap konsisten.
     */
    public function up(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('kode_spt')->default('090');
            $table->string('nama_opd')->default('DISKOMINFOSAN-BLG');

            $table->timestamps();
        });
    }
};
