@extends('layouts.app')

@section('content')
<div style="max-width:720px; margin:40px auto; background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">Message the Clinic</h1>
    <p style="color:#64748b;">Send a message to the clinic staff.</p>

    @if(session('success'))
        <div style="background:#ecfdf5; color:#166534; border:1px solid #bbf7d0; padding:12px 14px; border-radius:12px; margin-bottom:16px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:12px 14px; border-radius:12px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('messages.patient.send') }}">
        @csrf

        <input
            type="text"
            name="subject"
            placeholder="Subject"
            value="{{ old('subject') }}"
            style="width:100%; min-height:42px; padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px; margin-bottom:12px; box-sizing:border-box;"
        >

        <textarea
            name="message_body"
            placeholder="Type your message..."
            required
            style="width:100%; min-height:140px; padding:12px; border:1px solid #cbd5e1; border-radius:10px; box-sizing:border-box;"
        >{{ old('message_body') }}</textarea>

        <button type="submit" style="margin-top:12px; min-height:42px; padding:0 16px; border:none; border-radius:10px; background:#0f9d8a; color:#fff; font-weight:700; cursor:pointer;">
            Send Message
        </button>
    </form>
</div>
@endsection
