<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('request_code', 40)->unique();

            $table->foreignId('patient_id')->nullable()->constrained('patients', 'patient_id')->nullOnDelete();

            $table->boolean('is_guest')->default(true);
            $table->string('source_channel', 50)->default('web');

            $table->string('guest_first_name', 100)->nullable();
            $table->string('guest_middle_name', 100)->nullable();
            $table->string('guest_last_name', 100)->nullable();
            $table->string('guest_contact_number', 30)->nullable();
            $table->string('guest_email')->nullable();

            $table->string('sex', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_number', 30)->nullable();

            $table->foreignId('preferred_dentist_id')->nullable()->constrained('dentists', 'dentist_id')->nullOnDelete();
            $table->foreignId('service_id')->constrained('services', 'service_id')->restrictOnDelete();

            $table->date('preferred_date');
            $table->time('preferred_start_time');

            $table->text('notes')->nullable();
            $table->string('request_status', 50)->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
