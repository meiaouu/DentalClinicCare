@extends('staff.layouts.app')

@section('content')
<div style="max-width:900px; margin:0 auto; padding:20px;">

    <h1>Messages</h1>
    <p>List of messages from patients and guests.</p>

    <form method="GET" action="{{ route('staff.messages.index') }}" style="margin-bottom:15px;">
        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
        <button type="submit">Search</button>
    </form>

    @if(session('success'))
        <div style="background:#d1fae5; padding:10px; margin-bottom:10px;">
            {{ session('success') }}
        </div>
    @endif

    @forelse($threads as $thread)
        @php $latest = $thread->messages->first(); @endphp

        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <strong>{{ $thread->display_name }}</strong><br>

            <small>
                {{ $thread->subject ?? 'No subject' }}
            </small><br>

            <small>
                {{ $latest ? \Illuminate\Support\Str::limit($latest->message_body, 50) : 'No messages' }}
            </small><br>

            <small>
                Updated: {{ optional($thread->last_message_at)->diffForHumans() }}
            </small><br>

            <a href="{{ route('staff.messages.show', $thread->thread_id) }}">
                Open Conversation
            </a>
        </div>

    @empty
        <p>No messages found.</p>
    @endforelse

    {{ $threads->links() }}

</div>
@endsection
