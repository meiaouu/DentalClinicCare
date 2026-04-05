<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_option_values', function (Blueprint $table) {
            $table->id('value_id');
            $table->foreignId('option_id')->constrained('service_options', 'option_id')->cascadeOnDelete();
            $table->string('value_label', 150);
            $table->string('value_code', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_option_values');
    }
};
