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
            $table->uuid('certificate_uuid')->nullable()->unique()->after('certificate_name_changes');
            $table->timestamp('certificate_published_at')->nullable()->after('certificate_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn(['certificate_uuid', 'certificate_published_at']);
        });
    }
};
