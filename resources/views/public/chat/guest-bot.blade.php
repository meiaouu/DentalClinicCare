@extends('layouts.app')

@section('content')
<style>
    .guest-chat-wrapper {
        max-width: 900px;
        margin: 110px auto 40px;
        padding: 0 20px;
    }

    .guest-chat-card {
        background: #ffffff;
        border: 1px solid #dbe2ea;
        border-radius: 8px;
        overflow: hidden;
    }

    .guest-chat-header {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
        color: #0f172a;
    }

    .guest-chat-start-form {
        padding: 16px;
        display: grid;
        gap: 12px;
    }

    .guest-chat-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #0f172a;
    }

    .guest-chat-field {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        box-sizing: border-box;
    }

    .guest-chat-start-btn,
    .guest-chat-send-btn {
        border: none;
        background: #0f9d8a;
        color: #ffffff;
        padding: 10px 16px;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
    }

    .guest-chat-messages {
        height: 420px;
        overflow-y: auto;
        padding: 16px;
        background: #f8fafc;
    }

    .guest-chat-row {
        display: flex;
        margin-bottom: 12px;
    }

    .guest-chat-row-right {
        justify-content: flex-end;
    }

    .guest-chat-row-left {
        justify-content: flex-start;
    }

    .guest-chat-bubble {
        max-width: 70%;
        padding: 10px 12px;
        border-radius: 8px;
    }

    .guest-chat-bubble-guest {
        background: #0f9d8a;
        color: #ffffff;
        border: 1px solid #0f9d8a;
    }

    .guest-chat-bubble-bot {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #dbe2ea;
    }

    .guest-chat-sender {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .guest-chat-text {
        font-size: 14px;
        line-height: 1.5;
        word-break: break-word;
    }

    .guest-chat-time {
        font-size: 11px;
        opacity: 0.75;
        margin-top: 6px;
    }

    .guest-chat-form {
        padding: 14px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
    }

    .guest-chat-input {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        outline: none;
    }
</style>

<div class="guest-chat-wrapper">
    @php
        $guestConversationId = session('guest_chat_conversation_id');
        $guestConversation = null;

        if ($guestConversationId) {
            $guestConversation = \App\Models\Conversation::with('messages')
                ->where('conversation_id', $guestConversationId)
                ->where('is_guest', true)
                ->first();
        }
    @endphp

    <div class="guest-chat-card">
        <div class="guest-chat-header">
            Guest Chat Support
        </div>

        @if(!$guestConversation)
            <form method="POST" action="{{ route('chat.guest.start') }}" class="guest-chat-start-form">
                @csrf

                <div>
                    <label class="guest-chat-label">Your Name</label>
                    <input type="text" name="guest_name" required class="guest-chat-field">
                </div>

                <div>
                    <label class="guest-chat-label">Contact Number</label>
                    <input type="text" name="guest_contact_number" required class="guest-chat-field">
                </div>

                <div>
                    <button type="submit" class="guest-chat-start-btn">
                        Start Chat
                    </button>
                </div>
            </form>
        @else
            <div
                id="guestChatApp"
                data-send-url="{{ route('chat.guest.send') }}"
                data-fetch-url="{{ route('chat.guest.fetch', $guestConversation->conversation_id) }}"
            >
                <div id="guestChatMessages" class="guest-chat-messages">
                    @foreach($guestConversation->messages as $message)
                        @php
                            $isGuest = $message->sender_type === 'guest';
                            $rowClass = $isGuest ? 'guest-chat-row-right' : 'guest-chat-row-left';
                            $bubbleClass = $isGuest ? 'guest-chat-bubble-guest' : 'guest-chat-bubble-bot';
                            $label = $isGuest ? 'You' : 'Clinic Bot';
                        @endphp

                        <div class="guest-chat-row {{ $rowClass }}">
                            <div class="guest-chat-bubble {{ $bubbleClass }}">
                                <div class="guest-chat-sender">{{ $label }}</div>
                                <div class="guest-chat-text">{{ $message->message_text }}</div>
                                <div class="guest-chat-time">
                                    {{ optional($message->sent_at)->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form id="guestChatForm" class="guest-chat-form">
                    @csrf
                    <input
                        type="text"
                        name="message_text"
                        id="guest_message_text"
                        placeholder="Type your message..."
                        class="guest-chat-input"
                    >
                    <button type="submit" class="guest-chat-send-btn">
                        Send
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@if($guestConversation)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('guestChatApp');
    const form = document.getElementById('guestChatForm');
    const input = document.getElementById('guest_message_text');
    const messagesBox = document.getElementById('guestChatMessages');

    if (!app || !form || !input || !messagesBox) {
        return;
    }

    const sendUrl = app.dataset.sendUrl;
    const fetchUrl = app.dataset.fetchUrl;

    async function loadMessages() {
        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            messagesBox.innerHTML = '';

            (data.messages || []).forEach(function (message) {
                const isGuest = message.sender_type === 'guest';
                const rowClass = isGuest ? 'guest-chat-row-right' : 'guest-chat-row-left';
                const bubbleClass = isGuest ? 'guest-chat-bubble-guest' : 'guest-chat-bubble-bot';
                const label = isGuest ? 'You' : 'Clinic Bot';

                messagesBox.innerHTML += `
                    <div class="guest-chat-row ${rowClass}">
                        <div class="guest-chat-bubble ${bubbleClass}">
                            <div class="guest-chat-sender">${escapeHtml(label)}</div>
                            <div class="guest-chat-text">${escapeHtml(message.message_text)}</div>
                            <div class="guest-chat-time">${formatDate(message.sent_at)}</div>
                        </div>
                    </div>
                `;
            });

            messagesBox.scrollTop = messagesBox.scrollHeight;
        } catch (error) {
            console.error('Failed to load guest messages:', error);
        }
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const text = input.value.trim();
        if (!text) {
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                return;
            }

            input.value = '';
            await loadMessages();
        } catch (error) {
            console.error('Failed to send guest message:', error);
        }
    });

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDate(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value);
        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString();
    }

    loadMessages();
    setInterval(loadMessages, 5000);
});
</script>
@endif
@endsection
