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
        Schema::create('sppds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('spt_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('pegawai_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nomor_sppd')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppds');
    }
};