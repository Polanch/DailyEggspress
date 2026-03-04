@extends('layouts.security_layout')

@section('title', 'Forgot Password')

@section('content')
<div class="security-wrap">
    <div class="security-card">
        <h1>Forgot Password</h1>
        <p class="security-description">
            Enter the email address associated with your account, and we'll send you a verification code to reset your password.
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

        <form method="POST" action="{{ route('password.otp.send') }}" class="security-form">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    placeholder="your.email@example.com"
                    required
                >
            </div>

            <button type="submit" class="security-btn">Send Reset Code</button>
        </form>

        <div class="verify-box">
            📩 We'll send a 6-digit code to your email. Check your inbox (and spam folder) to complete the password reset process.
        </div>
    </div>
</div>
@endsection
