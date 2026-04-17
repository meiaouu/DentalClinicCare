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

    .chat-conversation-item.active {
        background: #f0fdfa;
        border-left: 3px solid #0f9d8a;
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

    .chat-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-header {
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .chat-back {
        font-size: 18px;
        text-decoration: none;
        color: #0f172a;
        padding: 4px 6px;
        border-radius: 4px;
        line-height: 1;
    }

    .chat-back:hover {
        background: #f1f5f9;
    }

    .chat-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .chat-status {
        font-size: 12px;
        color: #64748b;
        text-transform: capitalize;
    }

    .chat-header-right {
        position: relative;
    }

    .chat-menu {
        border: none;
        background: transparent;
        font-size: 18px;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        line-height: 1;
    }

    .chat-menu:hover {
        background: #f1f5f9;
    }

    .chat-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 34px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        width: 180px;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        z-index: 10;
    }

    .chat-dropdown form {
        margin: 0;
    }

    .chat-dropdown-item {
        width: 100%;
        display: block;
        padding: 10px 12px;
        font-size: 13px;
        text-align: left;
        border: none;
        background: #ffffff;
        cursor: pointer;
        color: #0f172a;
    }

    .chat-dropdown-item:hover {
        background: #f8fafc;
    }

    .chat-details-panel {
        display: none;
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        font-size: 13px;
        line-height: 1.6;
        color: #334155;
    }

    .chat-messages {
        flex: 1;
        padding: 14px;
        overflow-y: auto;
        background: #f8fafc;
        min-height: 420px;
    }

    .chat-row {
        display: flex;
        margin-bottom: 10px;
    }

    .chat-row-left {
        justify-content: flex-start;
    }

    .chat-row-right {
        justify-content: flex-end;
    }

    .chat-bubble {
        max-width: 70%;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 14px;
        line-height: 1.5;
    }

    .chat-bubble-patient,
    .chat-bubble-guest {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #dbe2ea;
    }

    .chat-bubble-staff {
        background: #0f9d8a;
        color: #ffffff;
        border: 1px solid #0f9d8a;
    }

    .chat-bubble-bot {
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #cbd5e1;
    }

    .chat-sender {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 4px;
        opacity: 0.85;
    }

    .chat-time {
        font-size: 11px;
        margin-top: 5px;
        opacity: 0.7;
    }

    .chat-suggest-wrap {
        padding: 10px 12px 0;
        background: #ffffff;
    }

    .chat-suggest-panel {
        display: none;
        background: #f8fafc;
        border: 1px solid #dbe2ea;
        border-radius: 14px;
        padding: 10px;
    }

    .chat-suggest-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
    }

    .chat-suggest-list {
        display: grid;
        gap: 8px;
    }

    .chat-suggest-item {
        border: 1px solid #c4b5fd;
        background: #ede9fe;
        color: #4c1d95;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 13px;
        line-height: 1.4;
        cursor: pointer;
        text-align: left;
        transition: 0.2s ease;
    }

    .chat-suggest-item:hover {
        background: #ddd6fe;
    }

    .chat-suggest-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .chat-suggest-action-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .chat-suggest-action-btn.primary {
        background: #0f9d8a;
        border-color: #0f9d8a;
        color: #ffffff;
    }

    .chat-form {
        border-top: 1px solid #e5e7eb;
        padding: 12px;
        display: flex;
        gap: 10px;
        background: #ffffff;
    }

    .chat-input {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        outline: none;
        font-size: 14px;
    }

    .chat-send {
        border: none;
        background: #0f9d8a;
        color: #ffffff;
        border-radius: 6px;
        padding: 10px 16px;
        font-weight: 700;
        cursor: pointer;
    }

    .chat-input:disabled,
    .chat-send:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    @media (max-width: 991px) {
        .chat-layout {
            grid-template-columns: 1fr;
        }

        .chat-conversation-list {
            max-height: 240px;
        }

        .chat-messages {
            min-height: 320px;
        }
    }
</style>

<div class="chat-layout">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            Conversations
        </div>

        <div class="chat-conversation-list">
            @forelse($conversations as $item)
                @php
                    $latest = $item->latestMessage->first();

                    $name = 'Unknown';
                    if ($item->patient) {
                        $name = trim($item->patient->first_name . ' ' . $item->patient->last_name);
                    } elseif ($item->guest_name) {
                        $name = $item->guest_name;
                    }

                    $isActive = (int) $conversation->conversation_id === (int) $item->conversation_id;

                    $unreadCount = $item->unreadMessages()
                        ->whereIn('sender_type', ['patient', 'guest'])
                        ->count();
                @endphp

                <a href="{{ route('staff.messages.show', $item->conversation_id) }}"
                   class="chat-conversation-item {{ $isActive ? 'active' : '' }}">
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

    <div
        class="chat-panel"
        id="staffChatApp"
        data-fetch-url="{{ route('staff.messages.fetch', $conversation->conversation_id) }}"
        data-suggest-url="{{ route('staff.ai.suggest-reply', ['conversation' => $conversation->conversation_id]) }}"
        data-csrf-token="{{ csrf_token() }}"
        data-conversation-status="{{ $conversation->conversation_status }}"
    >
        <div class="chat-header">
            <div class="chat-header-left">
                <a href="{{ route('staff.messages.index') }}" class="chat-back">←</a>

                <div>
                    <div class="chat-name">
                        @if($conversation->patient)
                            {{ $conversation->patient->first_name }} {{ $conversation->patient->last_name }}
                        @elseif($conversation->guest_name)
                            {{ $conversation->guest_name }}
                        @else
                            Conversation
                        @endif
                    </div>

                    <div class="chat-status">
                        {{ str_replace('_', ' ', $conversation->conversation_status) }}
                    </div>
                </div>
            </div>

            <div class="chat-header-right">
                <button id="menuBtn" class="chat-menu" type="button">⋮</button>

                <div id="menuDropdown" class="chat-dropdown">
                    <button type="button" class="chat-dropdown-item" id="viewDetailsBtn">
                        View Details
                    </button>

                    @if($conversation->conversation_status !== 'closed')
                        <form method="POST" action="{{ route('staff.messages.close', $conversation->conversation_id) }}">
                            @csrf
                            <button type="submit" class="chat-dropdown-item">
                                Mark as Closed
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('staff.messages.reopen', $conversation->conversation_id) }}">
                            @csrf
                            <button type="submit" class="chat-dropdown-item">
                                Reopen Conversation
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div id="chatDetailsPanel" class="chat-details-panel">
            <div>
                <strong>Name:</strong>
                @if($conversation->patient)
                    {{ $conversation->patient->first_name }} {{ $conversation->patient->last_name }}
                @elseif($conversation->guest_name)
                    {{ $conversation->guest_name }}
                @else
                    Conversation
                @endif
            </div>

            <div>
                <strong>Status:</strong>
                {{ str_replace('_', ' ', $conversation->conversation_status) }}
            </div>

            <div>
                <strong>Type:</strong>
                {{ $conversation->is_guest ? 'Guest' : 'Patient' }}
            </div>

            <div>
                <strong>Created:</strong>
                {{ optional($conversation->created_at)->format('M d, Y h:i A') }}
            </div>
        </div>

        <div class="chat-messages" id="staffChatMessages">
            @foreach($conversation->messages as $message)
                @php
                    $senderType = $message->sender_type;

                    if ($senderType === 'staff') {
                        $rowClass = 'chat-row-right';
                        $bubbleClass = 'chat-bubble-staff';
                        $senderLabel = 'You';
                    } elseif ($senderType === 'bot') {
                        $rowClass = 'chat-row-left';
                        $bubbleClass = 'chat-bubble-bot';
                        $senderLabel = 'Clinic Bot';
                    } elseif ($senderType === 'guest') {
                        $rowClass = 'chat-row-left';
                        $bubbleClass = 'chat-bubble-guest';
                        $senderLabel = 'Guest';
                    } else {
                        $rowClass = 'chat-row-left';
                        $bubbleClass = 'chat-bubble-patient';
                        $senderLabel = 'Patient';
                    }
                @endphp

                <div class="chat-row {{ $rowClass }}">
                    <div class="chat-bubble {{ $bubbleClass }}">
                        <div class="chat-sender">{{ $senderLabel }}</div>
                        <div>{{ $message->message_text }}</div>
                        <div class="chat-time">
                            {{ optional($message->sent_at)->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($conversation->conversation_status !== 'closed')
            <div class="chat-suggest-wrap">
                <div id="chatSuggestPanel" class="chat-suggest-panel">
                    <div class="chat-suggest-title">Suggested Replies</div>
                    <div id="chatSuggestList" class="chat-suggest-list"></div>

                    <div class="chat-suggest-actions">
                        <button type="button" id="refreshSuggestionBtn" class="chat-suggest-action-btn">
                            Refresh
                        </button>
                        <button type="button" id="hideSuggestionBtn" class="chat-suggest-action-btn">
                            Hide
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($conversation->conversation_status === 'closed')
            <div class="chat-form">
                <input
                    type="text"
                    id="staffReplyInput"
                    class="chat-input"
                    value="This conversation is closed."
                    disabled
                >
                <button type="button" class="chat-send" disabled>Send</button>
            </div>
        @else
            <form class="chat-form" method="POST" action="{{ route('staff.messages.reply', $conversation->conversation_id) }}">
                @csrf
                <input
                    type="text"
                    name="message_text"
                    id="staffReplyInput"
                    class="chat-input"
                    placeholder="Type your reply..."
                    required
                >
                <button type="submit" class="chat-send">Send</button>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('staffChatApp');
    const messagesBox = document.getElementById('staffChatMessages');
    const replyInput = document.getElementById('staffReplyInput');

    const menuBtn = document.getElementById('menuBtn');
    const dropdown = document.getElementById('menuDropdown');
    const viewDetailsBtn = document.getElementById('viewDetailsBtn');
    const chatDetailsPanel = document.getElementById('chatDetailsPanel');

    const suggestPanel = document.getElementById('chatSuggestPanel');
    const suggestList = document.getElementById('chatSuggestList');
    const refreshSuggestionBtn = document.getElementById('refreshSuggestionBtn');
    const hideSuggestionBtn = document.getElementById('hideSuggestionBtn');

    if (menuBtn && dropdown) {
        menuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', function () {
            dropdown.style.display = 'none';
        });
    }

    if (viewDetailsBtn && chatDetailsPanel) {
        viewDetailsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            chatDetailsPanel.style.display = chatDetailsPanel.style.display === 'block' ? 'none' : 'block';
            dropdown.style.display = 'none';
        });
    }

    if (!app || !messagesBox) {
        return;
    }

    const fetchUrl = app.dataset.fetchUrl;
    const suggestUrl = app.dataset.suggestUrl;
    const csrfToken = app.dataset.csrfToken;
    const conversationStatus = app.dataset.conversationStatus;

    let lastSuggestKey = '';

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

            let latestExternalMessage = null;

            (data.messages || []).forEach(function (message) {
                let rowClass = 'chat-row-left';
                let bubbleClass = 'chat-bubble-patient';
                let senderLabel = 'Patient';

                if (message.sender_type === 'staff') {
                    rowClass = 'chat-row-right';
                    bubbleClass = 'chat-bubble-staff';
                    senderLabel = 'You';
                } else if (message.sender_type === 'bot') {
                    rowClass = 'chat-row-left';
                    bubbleClass = 'chat-bubble-bot';
                    senderLabel = 'Clinic Bot';
                } else if (message.sender_type === 'guest') {
                    rowClass = 'chat-row-left';
                    bubbleClass = 'chat-bubble-guest';
                    senderLabel = 'Guest';
                }

                if (message.sender_type === 'patient' || message.sender_type === 'guest') {
                    latestExternalMessage = message;
                }

                messagesBox.innerHTML += `
                    <div class="chat-row ${rowClass}">
                        <div class="chat-bubble ${bubbleClass}">
                            <div class="chat-sender">${escapeHtml(senderLabel)}</div>
                            <div>${escapeHtml(message.message_text || message.message_body || '')}</div>
                            <div class="chat-time">${formatDate(message.sent_at)}</div>
                        </div>
                    </div>
                `;
            });

            messagesBox.scrollTop = messagesBox.scrollHeight;

            await tryAutoSuggest(latestExternalMessage, false);
        } catch (error) {
            console.error('Failed to load staff messages:', error);
        }
    }

    async function tryAutoSuggest(latestExternalMessage, forceRefresh = false) {
        if (!replyInput || !suggestPanel || !suggestList) {
            return;
        }

        if (conversationStatus === 'closed') {
            hideSuggestions();
            return;
        }

        if (!latestExternalMessage) {
            hideSuggestions();
            return;
        }

        const latestText = String(
            latestExternalMessage.message_text || latestExternalMessage.message_body || ''
        ).trim();

        if (!latestText) {
            hideSuggestions();
            return;
        }

        const suggestKey = `${latestExternalMessage.message_id || ''}:${latestText}`;

        if (!forceRefresh && lastSuggestKey === suggestKey) {
            return;
        }

        try {
            const response = await fetch(suggestUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    latest_message: latestText
                })
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => null);
                console.error('Suggest request failed:', response.status, errorData);

                renderSuggestions([
                    'Thank you for your message. Our clinic staff will assist you shortly.',
                    'We have received your message and will get back to you as soon as possible.',
                    'Thank you. Please wait while clinic staff reviews your concern.'
                ]);

                return;
            }

            const data = await response.json();

            if (data.suggested_reply) {
                lastSuggestKey = suggestKey;

                renderSuggestions([
                    data.suggested_reply,
                    'Thank you for your message. Our clinic staff will assist you shortly.',
                    'We have received your concern and will respond as soon as possible.'
                ]);
            } else {
                renderSuggestions([
                    'Thank you for your message. Our clinic staff will assist you shortly.',
                    'We have received your concern and will get back to you soon.'
                ]);
            }
        } catch (error) {
            console.error('Failed to get AI suggestion:', error);

            renderSuggestions([
                'Thank you for your message. Our clinic staff will assist you shortly.',
                'We have received your concern and will get back to you soon.'
            ]);
        }
    }

    function renderSuggestions(items) {
        const cleanItems = (items || []).filter(Boolean);

        if (!cleanItems.length) {
            hideSuggestions();
            return;
        }

        suggestList.innerHTML = '';

        cleanItems.forEach(function (text) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chat-suggest-item';
            btn.textContent = text;

            btn.addEventListener('click', function () {
                if (!replyInput) {
                    return;
                }

                replyInput.value = text;
                replyInput.focus();
            });

            suggestList.appendChild(btn);
        });

        suggestPanel.style.display = 'block';
    }

    function hideSuggestions() {
        if (!suggestPanel || !suggestList) {
            return;
        }

        suggestList.innerHTML = '';
        suggestPanel.style.display = 'none';
    }

    if (refreshSuggestionBtn) {
        refreshSuggestionBtn.addEventListener('click', async function () {
            lastSuggestKey = '';
            await loadMessages();
        });
    }

    if (hideSuggestionBtn) {
        hideSuggestionBtn.addEventListener('click', function () {
            hideSuggestions();
        });
    }

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
