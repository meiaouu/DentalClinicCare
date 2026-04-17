@extends('staff.layouts.app')

@section('staff_content')
<style>
    .staff-chat-page-fix {
        height: calc(100vh - 110px);
        overflow: hidden;
    }

    .chat-shell {
        display: grid;
        grid-template-columns: 220px minmax(0, 1fr);
        gap: 12px;
        height: 100%;
        overflow: hidden;
    }

    .chat-sidebar,
    .chat-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        min-height: 0;
    }

    .chat-sidebar {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .chat-sidebar-header {
        flex: 0 0 auto;
        padding: 12px 14px;
        border-bottom: 1px solid #eef2f7;
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        background: #ffffff;
    }

    .chat-sidebar-list {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .chat-sidebar-item {
        display: block;
        padding: 10px 12px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f3f4f6;
        background: #ffffff;
        transition: background 0.18s ease;
    }

    .chat-sidebar-item:hover {
        background: #f9fafb;
    }

    .chat-sidebar-item.active {
        background: #eff6ff;
    }

    .chat-sidebar-name {
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 3px;
        line-height: 1.3;
    }

    .chat-sidebar-preview {
        font-size: 11px;
        color: #6b7280;
        line-height: 1.35;
    }

    .chat-empty-list {
        padding: 14px;
        color: #6b7280;
        font-size: 12px;
    }

    .chat-panel {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
        background: #f8fafc;
    }

    .chat-panel-header {
        flex: 0 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
        background: #ffffff;
    }

    .chat-user-block {
        min-width: 0;
    }

    .chat-user-name {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
    }

    .chat-user-sub {
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
    }

    .chat-status {
        display: inline-flex;
        align-items: center;
        margin-top: 7px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .chat-status-open {
        background: #dcfce7;
        color: #166534;
    }

    .chat-status-closed {
        background: #f3f4f6;
        color: #374151;
    }

    .chat-status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .chat-status-bot {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .chat-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .chat-btn {
        border: none;
        border-radius: 10px;
        padding: 9px 13px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }

    .chat-btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .chat-btn-danger {
        background: #ef4444;
        color: #ffffff;
    }

    .chat-btn-success {
        background: #10b981;
        color: #ffffff;
    }

    .chat-messages {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 18px 16px;
        background: #f5f6f8;
    }

    .chat-alert-success,
    .chat-alert-error {
        margin-bottom: 12px;
        padding: 11px 13px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .chat-alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .chat-alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .chat-message-row {
        display: flex;
        margin-bottom: 14px;
    }

    .chat-message-row.staff {
        justify-content: flex-end;
    }

    .chat-message-row.patient,
    .chat-message-row.guest,
    .chat-message-row.bot {
        justify-content: flex-start;
    }

    .chat-message-wrap {
        display: flex;
        flex-direction: column;
        max-width: 72%;
    }

    .chat-message-row.staff .chat-message-wrap {
        align-items: flex-end;
    }

    .chat-message-row.patient .chat-message-wrap,
    .chat-message-row.guest .chat-message-wrap,
    .chat-message-row.bot .chat-message-wrap {
        align-items: flex-start;
    }

    .chat-message-meta {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 5px;
        padding: 0 4px;
        line-height: 1.2;
    }

    .chat-bubble {
        width: fit-content;
        max-width: 100%;
        padding: 12px 14px;
        border-radius: 16px;
        font-size: 13px;
        line-height: 0.85;
        white-space: pre-wrap;
        word-break: break-word;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }

    .chat-bubble.staff {
        background: #cfe5f8;
        color: #1f2937;
        border-bottom-right-radius: 5px;
    }

    .chat-bubble.patient,
    .chat-bubble.guest {
        background: #ececec;
        color: #374151;
        border-bottom-left-radius: 5px;
    }

    .chat-bubble.bot {
        background: #f3e8ff;
        color: #5b21b6;
        border: 1px solid #e9d5ff;
        border-bottom-left-radius: 5px;
        position: relative;
    }

    .chat-bubble.bot::before {
        content: "BOT";
        position: absolute;
        top: -8px;
        left: 10px;
        font-size: 9px;
        font-weight: 800;
        background: #7c3aed;
        color: #fff;
        padding: 2px 6px;
        border-radius: 999px;
    }

    .chat-empty-state {
        padding: 22px 16px;
        text-align: center;
        color: #6b7280;
        font-size: 13px;
    }

    .chat-reply-box {
        flex: 0 0 auto;
        padding: 10px 12px;
        border-top: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .chat-reply-form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        align-items: center;
    }

    .chat-reply-textarea {
        width: 100%;
        min-height: 46px;
        max-height: 110px;
        resize: vertical;
        border: 1px solid #d1d5db;
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
        background: #ffffff;
    }

    .chat-send-btn {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 999px;
        background: #4f97d1;
        color: #ffffff;
        font-size: 18px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-send-btn span {
        transform: translateX(1px);
        display: inline-block;
    }

    @media (max-width: 991px) {
        .staff-chat-page-fix {
            height: auto;
            overflow: visible;
        }

        .chat-shell {
            grid-template-columns: 1fr;
            height: auto;
            overflow: visible;
        }

        .chat-sidebar {
            max-height: 220px;
        }

        .chat-panel {
            height: calc(100vh - 180px);
        }

        .chat-panel-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .chat-header-actions {
            justify-content: flex-start;
        }

        .chat-message-wrap {
            max-width: 88%;
        }
    }

    .chat-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    background: #f3f4f6;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.2s ease;
}

.chat-back-btn:hover {
    background: #e5e7eb;
}
</style>

@php
    $displayName = 'Unknown';

    if ($conversation->patient) {
        $displayName = trim(collect([
            $conversation->patient->first_name ?? null,
            $conversation->patient->middle_name ?? null,
            $conversation->patient->last_name ?? null,
        ])->filter()->implode(' '));
    } elseif (isset($conversation->guest_name) && filled($conversation->guest_name)) {
        $displayName = $conversation->guest_name;
    } else {
        $displayName = 'Guest Conversation #' . $conversation->conversation_id;
    }

    $contactNumber = $conversation->patient->contact_number
        ?? ($conversation->guest_contact_number ?? null);

    $status = $conversation->conversation_status ?? 'open';

    $statusClass = 'chat-status-open';
    if ($status === 'closed') {
        $statusClass = 'chat-status-closed';
    } elseif ($status === 'pending_staff') {
        $statusClass = 'chat-status-pending';
    } elseif ($status === 'bot_only') {
        $statusClass = 'chat-status-bot';
    }
@endphp

<div class="staff-chat-page-fix">
    <div class="chat-shell">
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                Chats
            </div>

            <div class="chat-sidebar-list">
                @forelse($conversations as $item)
                    @php
                        $itemLatest = $item->messages->first();

                        $itemName = 'Unknown';
                        if ($item->patient) {
                            $itemName = trim(collect([
                                $item->patient->first_name ?? null,
                                $item->patient->middle_name ?? null,
                                $item->patient->last_name ?? null,
                            ])->filter()->implode(' '));
                        } elseif (isset($item->guest_name) && filled($item->guest_name)) {
                            $itemName = $item->guest_name;
                        } else {
                            $itemName = 'Guest Conversation #' . $item->conversation_id;
                        }

                        $itemPreview = 'No messages yet';
                        if ($itemLatest) {
                            $itemPreviewText = $itemLatest->message_text ?? $itemLatest->message_body ?? '';
                            if ($itemPreviewText !== '') {
                                $itemPreview = \Illuminate\Support\Str::limit($itemPreviewText, 28);
                            }
                        }
                    @endphp

                    <a
                        href="{{ route('staff.messages.show', $item->conversation_id) }}"
                        class="chat-sidebar-item {{ (int) $item->conversation_id === (int) $conversation->conversation_id ? 'active' : '' }}"
                    >
                        <div class="chat-sidebar-name">{{ $itemName }}</div>
                        <div class="chat-sidebar-preview">{{ $itemPreview }}</div>
                    </a>
                @empty
                    <div class="chat-empty-list">
                        No conversations available.
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="chat-panel">
            <div class="chat-panel-header">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('staff.messages.index') }}" class="chat-back-btn">
            <
        </a>

        <div class="chat-user-block">
            <h2 class="chat-user-name">{{ $displayName }}</h2>
            <div class="chat-user-sub">
                {{ $contactNumber ?: 'No contact number available' }}
            </div>
            <div class="chat-status {{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $status)) }}
            </div>
        </div>
    </div>

                <div class="chat-header-actions">
                    <form method="POST" action="{{ route('staff.messages.assign', $conversation->conversation_id) }}">
                        @csrf
                        <button type="submit" class="chat-btn chat-btn-primary">Assign</button>
                    </form>

                    @if(($conversation->conversation_status ?? null) !== 'closed')
                        <form method="POST" action="{{ route('staff.messages.close', $conversation->conversation_id) }}">
                            @csrf
                            <button type="submit" class="chat-btn chat-btn-danger">Close</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('staff.messages.reopen', $conversation->conversation_id) }}">
                            @csrf
                            <button type="submit" class="chat-btn chat-btn-success">Reopen</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                @if(session('success'))
                    <div class="chat-alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="chat-alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                @forelse($conversation->messages as $message)
                    @php
                        $senderType = $message->sender_type ?? 'unknown';
                        $bubbleType = in_array($senderType, ['staff', 'patient', 'guest', 'bot'], true) ? $senderType : 'patient';
                        $messageText = $message->message_text ?? $message->message_body ?? '[Empty message]';
                    @endphp

                    <div class="chat-message-row {{ $bubbleType }}">
                        <div class="chat-message-wrap">
                            <div class="chat-message-meta">
                                @if($senderType === 'bot')
                                    🤖 Bot
                                @elseif($senderType === 'staff')
                                    You
                                @else
                                    {{ ucfirst($senderType) }}
                                @endif

                                @if(!empty($message->sent_at))
                                    • {{ \Carbon\Carbon::parse($message->sent_at)->format('h:i A') }}
                                @endif
                            </div>

                            <div class="chat-bubble {{ $bubbleType }}">
                                {{ $messageText }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="chat-empty-state">
                        No messages yet.
                    </div>
                @endforelse
            </div>

            @if(($conversation->conversation_status ?? null) !== 'closed')
                <div class="chat-reply-box">
                    <form method="POST" action="{{ route('staff.messages.reply', $conversation->conversation_id) }}" class="chat-reply-form">
                        @csrf
                        <textarea
                            name="message_text"
                            class="chat-reply-textarea"
                            placeholder="Type something to send..."
                            required
                        >{{ old('message_text') }}</textarea>

                        <button type="submit" class="chat-send-btn" aria-label="Send message">
                            <span>➤</span>
                        </button>
                    </form>
                </div>
            @endif
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>
@endsection
