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
        Schema::table('protocols', function (Blueprint $table) {
            // Tambahkan foreign key untuk reviewer_kelompok_id
            $table->foreignId('reviewer_kelompok_id')
                  ->nullable() // 'nullable' karena mungkin belum di-assign saat dibuat
                  ->after('user_id') // Atur posisi (opsional)
                  ->constrained('reviewer_kelompoks') // Sesuaikan nama tabel jika perlu
                  ->onDelete('set null'); // Jika kelompok dihapus, set ID ke null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->dropForeign(['reviewer_kelompok_id']);
            $table->dropColumn('reviewer_kelompok_id');
        });
    }
};
