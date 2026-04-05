@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Register</h2>

    <form method="POST" action="{{ route('register.submit') }}">
        @csrf

        <input type="hidden" name="redirect" value="{{ $redirect }}">

        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-control">
                <option value="">Select Sex</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Birth Date</label>
            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control" placeholder="09XXXXXXXXX or +639XXXXXXXXX" value="{{ old('contact_number') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Register</button>
    </form>
</div>
@endsection
