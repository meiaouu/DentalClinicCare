<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('message_id');

            $table->unsignedBigInteger('thread_id');
            $table->unsignedBigInteger('sender_user_id')->nullable();

            $table->string('sender_type', 20); // staff, patient, guest
            $table->string('guest_name', 150)->nullable();

            $table->text('message_body');
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index('thread_id');
            $table->index('sender_user_id');
            $table->index('sender_type');
            $table->index('read_at');
            $table->index('created_at');

            $table->foreign('thread_id')
                ->references('thread_id')
                ->on('message_threads')
                ->cascadeOnDelete();

            $table->foreign('sender_user_id')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
