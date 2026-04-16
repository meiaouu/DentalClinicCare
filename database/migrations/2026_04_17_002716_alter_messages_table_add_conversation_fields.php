<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'conversation_id')) {
                $table->unsignedBigInteger('conversation_id')->nullable()->after('message_id');
            }

            if (!Schema::hasColumn('messages', 'sender_user_id')) {
                $table->unsignedBigInteger('sender_user_id')->nullable()->after('conversation_id');
            }

            if (!Schema::hasColumn('messages', 'sender_type')) {
                $table->string('sender_type', 20)->default('patient')->after('sender_user_id');
            }

            if (!Schema::hasColumn('messages', 'message_text')) {
                $table->text('message_text')->nullable()->after('sender_type');
            }

            if (!Schema::hasColumn('messages', 'is_bot_reply')) {
                $table->boolean('is_bot_reply')->default(false)->after('message_text');
            }

            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_bot_reply');
            }

            if (!Schema::hasColumn('messages', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('read_at');
            }
        });

        if (Schema::hasColumn('messages', 'sender_type')) {
            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->index('sender_type', 'messages_sender_type_index');
                });
            } catch (\Throwable $e) {
                // ignore if index already exists
            }
        }

        if (Schema::hasColumn('messages', 'conversation_id')) {
            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->index('conversation_id', 'messages_conversation_id_manual_index');
                });
            } catch (\Throwable $e) {
                // ignore if index already exists
            }
        }

        if (Schema::hasColumn('messages', 'sender_user_id')) {
            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->index('sender_user_id', 'messages_sender_user_id_manual_index');
                });
            } catch (\Throwable $e) {
                // ignore if index already exists
            }
        }
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->dropIndex('messages_sender_type_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('messages_conversation_id_manual_index');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('messages_sender_user_id_manual_index');
            } catch (\Throwable $e) {
            }

            foreach ([
                'conversation_id',
                'sender_user_id',
                'sender_type',
                'message_text',
                'is_bot_reply',
                'read_at',
                'sent_at',
            ] as $column) {
                if (Schema::hasColumn('messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
