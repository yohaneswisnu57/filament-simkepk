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

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // kolom data
            $table->string('namadokumen'); // teks nama dokumen
            $table->string('jenisdokumen'); // bisa dijadikan foreign key kalau ada master jenis dokumen
            // relasi user
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // relasi ke protocols
            $table->foreignId('protocol_id')->constrained('protocols')->onDelete('cascade');

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
        Schema::dropIfExists('documents');
    }
};
