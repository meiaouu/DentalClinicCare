@extends('staff.layouts.app')

@section('content')
<style>
    .page { display:grid; gap:16px; max-width:900px; }
    .card {
        background:#fff;
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:16px;
    }

    .title {
        margin:0 0 6px;
        font-size:24px;
        font-weight:800;
        color:#0f172a;
    }

    .sub {
        margin:0;
        font-size:14px;
        color:#64748b;
    }

    .messages {
        display:grid;
        gap:10px;
    }

    .message {
        border:1px solid #e2e8f0;
        border-radius:12px;
        padding:12px;
        background:#f8fafc;
    }

    .message.staff {
        background:#eff6ff;
        border-color:#bfdbfe;
    }

    .meta {
        font-size:12px;
        color:#64748b;
        margin-bottom:6px;
    }

    .body {
        font-size:14px;
        color:#0f172a;
        line-height:1.6;
        white-space:pre-wrap;
    }

    .textarea {
        width:100%;
        min-height:120px;
        border:1px solid #cbd5e1;
        border-radius:10px;
        padding:12px;
        font-size:14px;
        resize:vertical;
        box-sizing:border-box;
    }

    .btn {
        margin-top:10px;
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
</style>

<div class="page">
    <div class="card">
        <h1 class="title">{{ $thread->display_name }}</h1>
        <p class="sub">{{ $thread->subject ?? 'Conversation' }}</p>
    </div>

    @if(session('success'))
        <div class="card" style="color:#166534; background:#ecfdf5; border-color:#bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="messages">
            @forelse($thread->messages as $message)
                <div class="message {{ $message->sender_type === 'staff' ? 'staff' : '' }}">
                    <div class="meta">
                        <strong>
                            @if($message->sender_type === 'staff')
                                Staff
                            @elseif($message->sender_type === 'patient')
                                Patient
                            @else
                                Guest
                            @endif
                        </strong>
                        • {{ optional($message->created_at)->format('Y-m-d h:i A') }}
                    </div>

                    <div class="body">{{ $message->message_body }}</div>
                </div>
            @empty
                <div>No messages yet.</div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('staff.messages.reply', $thread->thread_id) }}">
            @csrf
            <textarea name="message_body" class="textarea" placeholder="Type your reply here..." required>{{ old('message_body') }}</textarea>
            <button type="submit" class="btn">Send Reply</button>
        </form>
    </div>
</div>
@endsection
