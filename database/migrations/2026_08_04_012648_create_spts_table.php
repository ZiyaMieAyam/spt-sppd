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
        Schema::create('spts', function (Blueprint $table) {
            $table->id();

            $table->enum('jenis_perjalanan', [
                'Dalam Daerah',
                'Luar Daerah',
            ]);

            $table->string('nomor_spt')->unique();

            $table->date('tanggal_spt');

            $table->date('tanggal_berangkat');

            $table->date('tanggal_kembali');

            $table->text('perihal');

            $table->foreignId('kecamatan_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('desa')
                ->nullable();

            $table->foreignId('kota_tujuan_id')
                ->nullable()
                ->constrained('kota_tujuans')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spts');
    }
};
