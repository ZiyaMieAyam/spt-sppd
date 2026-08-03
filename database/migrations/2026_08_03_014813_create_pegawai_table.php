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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();

            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('pangkat');
            $table->string('golongan');
            $table->string('jabatan');
            $table->string('unit_kerja');
            $table->enum('status', ['ASN', 'Non ASN'])->default('ASN');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};