@extends('staff.layouts.app')

@section('content')
<style>
    .chat-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 12px;
        min-height: 72vh;
    }

    .chat-sidebar {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
    }

    .chat-sidebar-header {
        padding: 12px;
        font-size: 14px;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
        color: #0f172a;
    }

    .chat-conversation-list {
        max-height: 70vh;
        overflow-y: auto;
    }

    .chat-conversation-item {
        display: block;
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        color: #0f172a;
        background: #ffffff;
        transition: 0.2s ease;
    }

    .chat-conversation-item:hover {
        background: #f8fafc;
    }

    .chat-conversation-name {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .chat-conversation-preview {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }

    .chat-unread-badge {
        display: inline-block;
        margin-top: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        background: #dc2626;
        border-radius: 999px;
        padding: 2px 8px;
    }

    .chat-empty {
        padding: 14px;
        font-size: 13px;
        color: #64748b;
    }

    .chat-panel-empty {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 14px;
        min-height: 72vh;
    }

    @media (max-width: 991px) {
        .chat-layout {
            grid-template-columns: 1fr;
        }

        .chat-conversation-list {
            max-height: 240px;
        }

        .chat-panel-empty {
            min-height: 260px;
        }
    }
</style>

<div class="chat-layout">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            Conversations
        </div>

        <div class="chat-conversation-list">
            @forelse($conversations as $conversation)
                @php
                    $latest = $conversation->latestMessage->first();

                    $name = 'Unknown';
                    if ($conversation->patient) {
                        $name = trim($conversation->patient->first_name . ' ' . $conversation->patient->last_name);
                    } elseif ($conversation->guest_name) {
                        $name = $conversation->guest_name;
                    }

                    $unreadCount = $conversation->unreadMessages()
                        ->whereIn('sender_type', ['patient', 'guest'])
                        ->count();
                @endphp

                <a href="{{ route('staff.messages.show', $conversation->conversation_id) }}"
                   class="chat-conversation-item">
                    <div class="chat-conversation-name">{{ $name }}</div>

                    <div class="chat-conversation-preview">
                        {{ $latest?->message_text ? \Illuminate\Support\Str::limit($latest->message_text, 45) : 'No messages yet' }}
                    </div>

                    @if($unreadCount > 0)
                        <div class="chat-unread-badge">
                            {{ $unreadCount }} unread
                        </div>
                    @endif
                </a>
            @empty
                <div class="chat-empty">
                    You don’t have any conversations yet.
                </div>
            @endforelse
        </div>
    </div>

    <div class="chat-panel-empty">
        Select a conversation to start chatting.
    </div>
</div>
@endsection
