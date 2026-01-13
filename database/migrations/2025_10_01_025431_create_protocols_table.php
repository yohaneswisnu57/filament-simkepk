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
        Schema::disableForeignKeyConstraints();

        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            $table->string('perihal_pengajuan');
            $table->string('jenis_protocol')->nullable();
            $table->timestamp('tanggal_pengajuan');
            $table->smallInteger('status_id')->nullable();
            $table->string('uploadpernyataan')->nullable();
            $table->string('buktipembayaran')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('tgl_mulai_review')->nullable();
            $table->date('tgl_selesai_review')->nullable();
            $table->unsignedBigInteger('reviewer_kelompok_id')->nullable();
            $table->foreign('reviewer_kelompok_id')->references('id')->on('reviewer_kelompoks');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocols');
    }
};
