<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->bigIncrements('thread_id');

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('appointment_request_id')->nullable();

            $table->string('thread_type', 30)->default('general'); // patient, guest_request, general
            $table->string('subject', 150)->nullable();

            $table->unsignedBigInteger('last_message_by_user_id')->nullable();
            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            $table->index('patient_id');
            $table->index('appointment_request_id');
            $table->index('thread_type');
            $table->index('last_message_at');

            // Optional foreign keys if your schema is stable
            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->nullOnDelete();

            $table->foreign('appointment_request_id')
                ->references('request_id')
                ->on('appointment_requests')
                ->nullOnDelete();

            $table->foreign('last_message_by_user_id')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_threads');
    }
};
