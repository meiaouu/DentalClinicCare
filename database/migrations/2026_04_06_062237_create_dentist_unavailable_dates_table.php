<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dentist_unavailable_dates', function (Blueprint $table) {
            $table->id('unavailable_id');
            $table->unsignedBigInteger('dentist_id');
            $table->date('unavailable_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['dentist_id', 'unavailable_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dentist_unavailable_dates');
    }
};
