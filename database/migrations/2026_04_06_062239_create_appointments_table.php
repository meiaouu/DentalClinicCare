<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->unsignedBigInteger('appointment_request_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('dentist_id')->nullable();

            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['approved', 'completed', 'cancelled', 'no_show'])->default('approved');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['appointment_date', 'dentist_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
