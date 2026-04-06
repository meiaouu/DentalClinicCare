<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dentist_schedules', function (Blueprint $table) {
            $table->id('dentist_schedule_id');
            $table->unsignedBigInteger('dentist_id');
            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_available')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->index(['dentist_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dentist_schedules');
    }
};
