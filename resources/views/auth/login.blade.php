@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Login</h2>

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <input type="hidden" name="redirect" value="{{ $redirect }}">

        <div class="mb-3">
            <label class="form-label">Email/Username</label>
           <input type="text" name="login" value="{{ old('login') }}" placeholder="Email, username, or mobile number">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>

        @csrf
<input type="hidden" name="redirect" value="{{ $redirect ?? old('redirect') }}">
    </form>
</div>
@endsection
