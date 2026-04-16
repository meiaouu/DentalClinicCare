@extends('layouts.app')

@section('content')
<style>
    .clinic-chat-wrapper {
        max-width: 900px;
        margin: 100px auto 40px;
        padding: 0 20px;
    }

    .clinic-chat-card {
        background: #ffffff;
        border: 1px solid #dbe2ea;
        border-radius: 8px;
        overflow: hidden;
    }

    .clinic-chat-header {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
    }

    .clinic-chat-messages {
        height: 420px;
        overflow-y: auto;
        padding: 16px;
        background: #f8fafc;
    }

    .chat-row {
        margin-bottom: 12px;
        display: flex;
    }

    .chat-row-right {
        justify-content: flex-end;
    }

    .chat-row-left {
        justify-content: flex-start;
    }

    .chat-bubble {
        max-width: 70%;
        padding: 10px 12px;
        border-radius: 8px;
    }

    .chat-bubble-patient {
        background: #0f9d8a;
        color: #ffffff;
        border: 1px solid #0f9d8a;
    }

    .chat-bubble-staff,
    .chat-bubble-bot {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #dbe2ea;
    }

    .chat-label {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .chat-text {
        font-size: 14px;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .chat-time {
        font-size: 11px;
        opacity: .75;
        margin-top: 6px;
    }

    .clinic-chat-form {
        padding: 14px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
    }

    .clinic-chat-input {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        outline: none;
    }

    .clinic-chat-button {
        border: none;
        background: #0f9d8a;
        color: #ffffff;
        padding: 10px 16px;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
    }
</style>

<div
    class="clinic-chat-wrapper"
    id="patientChatApp"
    data-send-url="{{ route('messages.patient.send') }}"
    data-fetch-url="{{ route('messages.patient.fetch') }}"
>
    <div class="clinic-chat-card">
        <div class="clinic-chat-header">
            Clinic Chat
        </div>

        <div id="chatMessages" class="clinic-chat-messages">
            @foreach($conversation->messages as $message)
                @php
                    $isPatient = $message->sender_type === 'patient';
                    $rowClass = $isPatient ? 'chat-row-right' : 'chat-row-left';

                    if ($message->sender_type === 'bot') {
                        $senderLabel = 'Clinic Bot';
                        $bubbleClass = 'chat-bubble-bot';
                    } elseif ($message->sender_type === 'staff') {
                        $senderLabel = 'Clinic Staff';
                        $bubbleClass = 'chat-bubble-staff';
                    } else {
                        $senderLabel = 'You';
                        $bubbleClass = 'chat-bubble-patient';
                    }
                @endphp

                <div class="chat-row {{ $rowClass }}">
                    <div class="chat-bubble {{ $bubbleClass }}">
                        <div class="chat-label">{{ $senderLabel }}</div>
                        <div class="chat-text">{{ $message->message_text }}</div>
                        <div class="chat-time">
                            {{ optional($message->sent_at)->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form id="patientChatForm" class="clinic-chat-form">
            @csrf
            <input
                type="text"
                name="message_text"
                id="message_text"
                class="clinic-chat-input"
                placeholder="Type your message..."
            >
            <button type="submit" class="clinic-chat-button">Send</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('patientChatApp');
    const form = document.getElementById('patientChatForm');
    const input = document.getElementById('message_text');
    const messagesBox = document.getElementById('chatMessages');

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
                const isPatient = message.sender_type === 'patient';
                const rowClass = isPatient ? 'chat-row-right' : 'chat-row-left';

                let senderLabel = 'You';
                let bubbleClass = 'chat-bubble-patient';

                if (message.sender_type === 'staff') {
                    senderLabel = 'Clinic Staff';
                    bubbleClass = 'chat-bubble-staff';
                } else if (message.sender_type === 'bot') {
                    senderLabel = 'Clinic Bot';
                    bubbleClass = 'chat-bubble-bot';
                }

                messagesBox.innerHTML += `
                    <div class="chat-row ${rowClass}">
                        <div class="chat-bubble ${bubbleClass}">
                            <div class="chat-label">${escapeHtml(senderLabel)}</div>
                            <div class="chat-text">${escapeHtml(message.message_text)}</div>
                            <div class="chat-time">${formatDate(message.sent_at)}</div>
                        </div>
                    </div>
                `;
            });

            messagesBox.scrollTop = messagesBox.scrollHeight;
        } catch (error) {
            console.error('Failed to load messages:', error);
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
            console.error('Failed to send message:', error);
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
        if (!value) return '';
        const date = new Date(value);
        if (isNaN(date.getTime())) return value;
        return date.toLocaleString();
    }

    loadMessages();
    setInterval(loadMessages, 5000);
});
</script>
@endsection
