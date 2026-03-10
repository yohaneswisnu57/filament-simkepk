<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('protocol_reviewers', function (Blueprint $table): void {
            $table->string('feedback_status')->default('pending')->after('role_in_review');
            // 'pending'   = reviewer belum submit verdict
            // 'submitted' = reviewer sudah submit verdict
        });
    }

    public function down(): void
    {
        Schema::table('protocol_reviewers', function (Blueprint $table): void {
            $table->dropColumn('feedback_status');
        });
    }
};
