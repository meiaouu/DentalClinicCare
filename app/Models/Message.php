<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $primaryKey = 'message_id';

    protected $fillable = [
    'conversation_id',
    'sender_user_id',
    'sender_type',
    'message_text',
    'message_body',
    'is_bot_reply',
    'read_at',
    'sent_at',
];

    protected $casts = [
        'is_bot_reply' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id', 'user_id');
    }
}
