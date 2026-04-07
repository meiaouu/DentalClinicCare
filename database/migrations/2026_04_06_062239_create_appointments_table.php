<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->string('appointment_code', 50)->unique();

            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('dentist_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();

            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedInteger('estimated_duration_minutes')->default(30);
            $table->decimal('estimated_price', 10, 2)->default(0);
            $table->string('status', 50)->default('confirmed');

            $table->unsignedBigInteger('booked_by')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();

            $table->text('cancellation_reason')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('no_show_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('request_id')
                ->references('request_id')
                ->on('appointment_requests')
                ->nullOnDelete();

            $table->foreign('dentist_id')
                ->references('dentist_id')
                ->on('dentists')
                ->nullOnDelete();

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->nullOnDelete();

            $table->foreign('service_id')
                ->references('service_id')
                ->on('services')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
