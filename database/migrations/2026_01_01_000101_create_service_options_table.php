<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_options', function (Blueprint $table) {
            $table->id('option_id');
            $table->foreignId('service_id')->constrained('services', 'service_id')->cascadeOnDelete();
            $table->string('option_name', 150);
            $table->string('option_type', 50);
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_options');
    }
};
