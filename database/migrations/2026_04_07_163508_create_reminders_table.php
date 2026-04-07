<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id('reminder_id');
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('channel', 30)->default('email');
            $table->string('reminder_type', 50);
            $table->timestamp('scheduled_at');
            $table->string('status', 30)->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')
                ->references('appointment_id')
                ->on('appointments')
                ->onDelete('cascade');

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
