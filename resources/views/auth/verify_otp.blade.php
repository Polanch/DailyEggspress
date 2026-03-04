@extends('layouts.security_layout')

@section('title', 'Verify OTP')

@section('content')
<div class="security-wrap">
    <div class="security-card">
        <h1>Verify Your Code</h1>
        <p class="security-description">
            A 6-digit verification code has been sent to your email. Please enter it below to continue with your password reset.
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

        <form method="POST" action="{{ route('password.otp.verify') }}" class="security-form">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="otp">Enter 6-Digit Code</label>
                <input 
                    type="text" 
                    name="otp" 
                    id="otp" 
                    maxlength="6" 
                    placeholder="000000"
                    required 
                    pattern="\d{6}"
                    inputmode="numeric"
                    style="letter-spacing: 0.5em; font-size: 20px; font-weight: bold; text-align: center;"
                >
            </div>

            <div class="verify-box">
                🔐 This code is valid for 30 minutes. If you didn't receive it, check your spam folder or request a new one.
            </div>

            <button type="submit" class="security-btn">Verify Code</button>
        </form>
    </div>
</div>
@endsection
