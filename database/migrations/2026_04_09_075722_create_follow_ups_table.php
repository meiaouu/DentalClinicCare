<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->bigIncrements('follow_up_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('dentist_id');
            $table->unsignedBigInteger('treatment_id')->nullable();
            $table->date('recommended_date')->nullable();
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('patient_id');
            $table->index('dentist_id');
            $table->index('treatment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
