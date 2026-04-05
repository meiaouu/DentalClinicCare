<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dentists', function (Blueprint $table) {
            $table->id('dentist_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();

            $table->string('dentist_code', 50)->unique();
            $table->string('license_number', 100)->nullable()->unique();
            $table->string('specialization', 150)->nullable();
            $table->boolean('is_owner')->default(false);
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dentists');
    }
};
