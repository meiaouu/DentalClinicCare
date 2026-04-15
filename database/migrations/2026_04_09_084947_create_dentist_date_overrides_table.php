<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dentist_date_overrides', function (Blueprint $table) {
            $table->bigIncrements('override_id');
            $table->unsignedBigInteger('dentist_id');
            $table->date('override_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_available')->default(false);
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->unique(['dentist_id', 'override_date']);
            $table->index('dentist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dentist_date_overrides');
    }
};
