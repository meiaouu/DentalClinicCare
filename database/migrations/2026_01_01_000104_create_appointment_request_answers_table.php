<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_request_answers', function (Blueprint $table) {
            $table->id('request_answer_id');

            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('selected_value_id')->nullable();

            $table->text('answer_text')->nullable();
            $table->timestamps();

            $table->foreign('request_id')
                ->references('request_id')
                ->on('appointment_requests')
                ->onDelete('cascade');

            $table->foreign('option_id')
                ->references('option_id')
                ->on('service_options')
                ->onDelete('cascade');

            $table->foreign('selected_value_id')
                ->references('value_id')
                ->on('service_option_values')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_request_answers');
    }
};
