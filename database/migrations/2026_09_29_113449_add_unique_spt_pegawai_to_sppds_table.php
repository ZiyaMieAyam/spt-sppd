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
        Schema::table('sppds', function (Blueprint $table) {
            $table->unique(
                ['spt_id', 'pegawai_id'],
                'sppds_spt_id_pegawai_id_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            $table->dropUnique('sppds_spt_id_pegawai_id_unique');
        });
    }
};