<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('protocols', function (Blueprint $table): void {
            $table->string('fast_review_decision')->nullable()->after('reviewer_kelompok_id');
            // NULL          = bukan Fast Review / belum ada keputusan
            // 'Pending'     = ada reviewer yang belum submit
            // 'Exempted'    = semua verdict Exempted → admin bisa cetak certificate
            // 'Full Board'  = ada ≥1 verdict Full Board → eskalasi otomatis
        });
    }

    public function down(): void
    {
        Schema::table('protocols', function (Blueprint $table): void {
            $table->dropColumn('fast_review_decision');
        });
    }
};
