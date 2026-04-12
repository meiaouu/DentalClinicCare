<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_status_logs', function (Blueprint $table) {
            $table->bigIncrements('status_log_id');
            $table->unsignedBigInteger('appointment_id');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            $table->foreign('appointment_id')
                ->references('appointment_id')
                ->on('appointments')
                ->cascadeOnDelete();

            $table->foreign('changed_by')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_status_logs');
    }
};
