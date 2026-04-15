@extends('staff.layouts.app')

@section('content')
<div style="max-width:900px; margin:0 auto; padding:20px;">

    <h1>Message Request</h1>
    <p>This message came from a patient or guest.</p>

    @if(session('success'))
        <div style="background:#d1fae5; padding:10px; margin-bottom:10px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
        <strong>{{ $thread->display_name }}</strong><br>
        <small>{{ $thread->subject ?? 'Conversation' }}</small>
    </div>

    <div style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
        @forelse($thread->messages as $message)
            <div style="margin-bottom:10px;">
                <strong>
                    @if($message->sender_type === 'staff')
                        Staff
                    @elseif($message->sender_type === 'patient')
                        Patient
                    @else
                        {{ $message->guest_name ?? 'Guest' }}
                    @endif
                </strong>
                <small>({{ $message->created_at->format('Y-m-d H:i') }})</small>
                <br>
                {{ $message->message_body }}
            </div>
        @empty
            <p>No messages yet.</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('staff.messages.reply', $thread->thread_id) }}">
        @csrf

        <textarea name="message_body" placeholder="Type reply..." required style="width:100%; height:100px;"></textarea>

        <br><br>

        <button type="submit">Send Reply</button>
    </form>

</div>
@endsection
