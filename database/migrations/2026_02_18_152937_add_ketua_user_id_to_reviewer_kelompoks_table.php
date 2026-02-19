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
        Schema::table('reviewer_kelompoks', function (Blueprint $table) {
            if (! Schema::hasColumn('reviewer_kelompoks', 'ketua_user_id')) {
                $table->foreignId('ketua_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviewer_kelompoks', function (Blueprint $table) {
            if (Schema::hasColumn('reviewer_kelompoks', 'ketua_user_id')) {
                $table->dropColumn('ketua_user_id');
            }
        });
    }
};
