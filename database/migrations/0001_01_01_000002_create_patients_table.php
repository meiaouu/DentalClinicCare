<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();

            $table->string('patient_code', 50)->unique();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('contact_number', 30)->nullable();
            $table->string('email')->nullable();

            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_number', 30)->nullable();
            $table->text('notes')->nullable();
            $table->string('profile_status', 50)->default('active');

            $table->foreignId('created_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->boolean('is_guest_converted')->default(false);
            $table->unsignedBigInteger('converted_from_request_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
