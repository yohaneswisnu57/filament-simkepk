<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->text('revision_notes')->nullable()->after('status_id');
        });

        // Insert new status if it doesn't exist
        DB::table('status_reviews')->updateOrInsert(
            ['id' => 8],
            ['status_name' => 'REVISION REQUIRED', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn('revision_notes');
        });

        DB::table('status_reviews')->where('id', 8)->delete();
    }
};
