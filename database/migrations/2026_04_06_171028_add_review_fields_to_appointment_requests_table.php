<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('appointment_requests', 'reviewed_by_user_id')) {
                $table->unsignedBigInteger('reviewed_by_user_id')->nullable()->after('request_status');
            }

            if (!Schema::hasColumn('appointment_requests', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_user_id');
            }

            if (!Schema::hasColumn('appointment_requests', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('reviewed_at');
            }

            $table->foreign('reviewed_by_user_id')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('appointment_requests', 'reviewed_by_user_id')) {
                $table->dropForeign(['reviewed_by_user_id']);
            }

            if (Schema::hasColumn('appointment_requests', 'review_notes')) {
                $table->dropColumn('review_notes');
            }

            if (Schema::hasColumn('appointment_requests', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }

            if (Schema::hasColumn('appointment_requests', 'reviewed_by_user_id')) {
                $table->dropColumn('reviewed_by_user_id');
            }
        });
    }
};
