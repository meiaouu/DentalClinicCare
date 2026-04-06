<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinic_schedule_rules', function (Blueprint $table) {
            $table->id('rule_id');
            $table->unsignedTinyInteger('day_of_week'); // 0=Sun, 1=Mon...
            $table->boolean('is_open')->default(false);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_schedule_rules');
    }
};
