<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id('conversation_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('appointment_request_id')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->string('conversation_status', 30)->default('open');
            $table->boolean('is_guest')->default(false);
            $table->string('guest_name', 150)->nullable();
            $table->string('guest_contact_number', 30)->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->nullOnDelete();

            $table->foreign('appointment_request_id')
                ->references('request_id')
                ->on('appointment_requests')
                ->nullOnDelete();

            $table->foreign('handled_by')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['patient_id', 'conversation_status']);
            $table->index(['handled_by', 'conversation_status']);
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
