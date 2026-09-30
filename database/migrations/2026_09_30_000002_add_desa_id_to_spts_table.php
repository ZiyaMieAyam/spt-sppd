<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tahap 1 desa_id: tambah FK nullable berdampingan dengan kolom
     * string `desa`. Kolom `desa` TIDAK diubah/dihapus dan tetap
     * menjadi sumber output historis. Hapus aman: nullOnDelete agar
     * penghapusan master desa tidak ikut menghapus SPT.
     */
    public function up(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->foreignId('desa_id')
                ->nullable()
                ->after('desa')
                ->constrained('desas')
                ->nullOnDelete();
        });
    }

    /**
     * Rollback hanya menghapus FK/kolom desa_id.
     * Kolom `desa` tidak disentuh.
     */
    public function down(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropColumn('desa_id');
        });
    }
};
