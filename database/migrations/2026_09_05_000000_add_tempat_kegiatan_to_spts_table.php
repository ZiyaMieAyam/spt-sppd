<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->string('tempat_kegiatan')->nullable()->after('kota_tujuan_id');
        });
    }

    public function down(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->dropColumn('tempat_kegiatan');
        });
    }
};
