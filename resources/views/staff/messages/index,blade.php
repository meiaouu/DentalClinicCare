@extends('staff.layouts.app')

@section('content')
<style>
    .page { display:grid; gap:16px; }
    .header h1 { margin:0 0 6px; font-size:26px; font-weight:800; color:#0f172a; }
    .header p { margin:0; color:#64748b; font-size:14px; }

    .search-card,
    .thread-card {
        background:#fff;
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:16px;
    }

    .search-form {
        display:grid;
        grid-template-columns: 1fr auto;
        gap:10px;
    }

    .input {
        min-height:42px;
        border:1px solid #cbd5e1;
        border-radius:10px;
        padding:10px 12px;
        font-size:14px;
    }

    .btn {
        min-height:42px;
        padding:0 14px;
        border:none;
        border-radius:10px;
        background:#0f9d8a;
        color:#fff;
        font-size:14px;
        font-weight:700;
        cursor:pointer;
    }

    .list { display:grid; gap:12px; }

    .thread-top {
        display:flex;
        justify-content:space-between;
        gap:10px;
        flex-wrap:wrap;
        margin-bottom:8px;
    }

    .thread-name {
        font-size:15px;
        font-weight:800;
        color:#0f172a;
    }

    .thread-type {
        font-size:11px;
        font-weight:700;
        color:#475569;
        background:#f1f5f9;
        padding:4px 8px;
        border-radius:999px;
    }

    .thread-meta {
        font-size:13px;
        color:#64748b;
        line-height:1.5;
    }

    .thread-link {
        display:inline-block;
        margin-top:10px;
        text-decoration:none;
        font-size:13px;
        font-weight:700;
        color:#2563eb;
    }
</style>

<div class="page">
    <div class="header">
        <h1>Messages</h1>
        <p>Patient and guest request conversations.</p>
    </div>

    <div class="search-card">
        <form method="GET" action="{{ route('staff.messages.index') }}" class="search-form">
            <input
                type="text"
                name="search"
                class="input"
                placeholder="Search patient, guest, contact, or subject"
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

    <div class="list">
        @forelse($threads as $thread)
            @php
                $latest = $thread->messages->first();
            @endphp

            <div class="thread-card">
                <div class="thread-top">
                    <div>
                        <div class="thread-name">{{ $thread->display_name }}</div>
                        <div class="thread-meta">{{ $thread->subject ?? 'No subject' }}</div>
                    </div>

                    <div class="thread-type">
                        {{ str_replace('_', ' ', $thread->thread_type) }}
                    </div>
                </div>

                <div class="thread-meta">
                    @if($latest)
                        <strong>Latest:</strong> {{ \Illuminate\Support\Str::limit($latest->message_body, 120) }}<br>
                    @endif
                    <strong>Updated:</strong> {{ optional($thread->last_message_at)->diffForHumans() ?? '—' }}
                </div>

                <a href="{{ route('staff.messages.show', $thread->thread_id) }}" class="thread-link">
                    Open Conversation
                </a>
            </div>
        @empty
            <div class="thread-card">
                No messages found.
            </div>
        @endforelse
    </div>

    <div>
        {{ $threads->links() }}
    </div>
</div>
@endsection
