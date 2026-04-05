<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->string('clinic_name', 150);
            $table->time('open_time');
            $table->time('close_time');
            $table->unsignedInteger('slot_interval_minutes')->default(30);
            $table->unsignedInteger('default_no_show_minutes')->default(30);
            $table->boolean('allow_patient_cancel_pending')->default(true);
            $table->boolean('allow_patient_cancel_confirmed')->default(false);

            $table->string('contact_number', 30)->nullable();
            $table->string('clinic_email')->nullable();
            $table->string('clinic_location')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('messenger_url')->nullable();
            $table->string('instagram_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};
