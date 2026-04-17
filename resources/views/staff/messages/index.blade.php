@extends('staff.layouts.app')

@section('staff_content')
<style>
    .conversation-index-page {
        max-width: 100%;
        margin: 0 auto;
    }

    .conversation-index-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .conversation-index-header {
        padding: 16px 18px;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .conversation-index-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #111827;
    }

    .conversation-index-subtitle {
        margin: 6px 0 0;
        font-size: 13px;
        color: #6b7280;
    }

    .conversation-search-form {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .conversation-search-input {
        min-width: 240px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 13px;
        outline: none;
    }

    .conversation-search-button {
        padding: 10px 14px;
        border: none;
        border-radius: 10px;
        background: #2563eb;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .conversation-list {
        max-height: calc(100vh - 260px);
        overflow-y: auto;
    }

    .conversation-item {
        display: block;
        text-decoration: none;
        color: inherit;
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.18s ease;
    }

    .conversation-item:hover {
        background: #f8fafc;
    }

    .conversation-name {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .conversation-preview {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.4;
        margin-bottom: 8px;
    }

    .conversation-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .conversation-status {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .conversation-status-open {
        background: #dcfce7;
        color: #166534;
    }

    .conversation-status-closed {
        background: #f3f4f6;
        color: #374151;
    }

    .conversation-status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .conversation-status-bot {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .conversation-time {
        font-size: 11px;
        color: #9ca3af;
    }

    .conversation-empty {
        padding: 24px 18px;
        text-align: center;
        color: #6b7280;
        font-size: 13px;
    }

    .conversation-pagination {
        padding: 12px 16px;
        border-top: 1px solid #eef2f7;
        background: #fff;
    }

    .conversation-alert-success,
    .conversation-alert-error {
        margin: 12px 16px 0;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .conversation-alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .conversation-alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .conversation-index-header {
            align-items: stretch;
        }

        .conversation-search-form {
            width: 100%;
        }

        .conversation-search-input {
            min-width: 0;
            flex: 1;
        }

        .conversation-search-button {
            width: 100%;
        }

        .conversation-list {
            max-height: none;
        }
    }
</style>

<div class="conversation-index-page">
    <div class="conversation-index-card">
        <div class="conversation-index-header">
            <div>
                <h1 class="conversation-index-title">Conversations</h1>
                <p class="conversation-index-subtitle">Select a conversation to open the chat.</p>
            </div>

            <form method="GET" action="{{ route('staff.messages.index') }}" class="conversation-search-form">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search patient or guest..."
                    class="conversation-search-input"
                >
                <button type="submit" class="conversation-search-button">Search</button>
            </form>
        </div>

        @if(session('success'))
            <div class="conversation-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="conversation-alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="conversation-list">
            @forelse($conversations as $conversation)
                @php
                    $latest = $conversation->messages->first();

                    $name = 'Unknown';
                    if ($conversation->patient) {
                        $name = trim(collect([
                            $conversation->patient->first_name ?? null,
                            $conversation->patient->middle_name ?? null,
                            $conversation->patient->last_name ?? null,
                        ])->filter()->implode(' '));
                    } elseif (isset($conversation->guest_name) && filled($conversation->guest_name)) {
                        $name = $conversation->guest_name;
                    } else {
                        $name = 'Guest Conversation #' . $conversation->conversation_id;
                    }

                    $preview = 'No messages yet';
                    if ($latest) {
                        $previewText = $latest->message_text ?? $latest->message_body ?? '';
                        if ($previewText !== '') {
                            $preview = \Illuminate\Support\Str::limit($previewText, 70);
                        }
                    }

                    $status = $conversation->conversation_status ?? 'open';

                    $statusClass = 'conversation-status-open';
                    if ($status === 'closed') {
                        $statusClass = 'conversation-status-closed';
                    } elseif ($status === 'pending_staff') {
                        $statusClass = 'conversation-status-pending';
                    } elseif ($status === 'bot_only') {
                        $statusClass = 'conversation-status-bot';
                    }
                @endphp

                <a href="{{ route('staff.messages.show', $conversation->conversation_id) }}" class="conversation-item">
                    <div class="conversation-name">{{ $name }}</div>
                    <div class="conversation-preview">{{ $preview }}</div>

                    <div class="conversation-meta">
                        <div class="conversation-status {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </div>

                        <div class="conversation-time">
                            @if(!empty($conversation->updated_at))
                                {{ \Carbon\Carbon::parse($conversation->updated_at)->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="conversation-empty">
                    No conversations found.
                </div>
            @endforelse
        </div>

        <div class="conversation-pagination">
            {{ $conversations->links() }}
        </div>
    </div>
</div>
@endsection
