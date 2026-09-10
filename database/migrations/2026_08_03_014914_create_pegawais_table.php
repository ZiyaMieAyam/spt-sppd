<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
