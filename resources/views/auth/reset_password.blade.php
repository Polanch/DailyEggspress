@extends('layouts.security_layout')

@section('title', 'Reset Password')

@section('content')
<div class="security-wrap">
    <div class="security-card">
        <h1>Reset Your Password</h1>
        <p class="security-description">
            Enter your new password below. Make sure it's strong and secure.
        </p>

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}" class="security-form">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password">New Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="Enter your new password"
                    required
                    minlength="8"
                >
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    placeholder="Confirm your new password"
                    required
                    minlength="8"
                >
            </div>

            <div class="password-requirements">
                <strong>Password Requirements:</strong>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Mix of uppercase and lowercase letters</li>
                    <li>Include at least one number</li>
                    <li>Include at least one special character</li>
                </ul>
            </div>

            <button type="submit" class="security-btn">Reset Password</button>
        </form>
    </div>
</div>
@endsection
