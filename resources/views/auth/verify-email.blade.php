@extends('layouts.security_layout')

@section('title', 'Email Verification')

@section('content')
<div class="security-wrap">
    <div class="security-card">
        <div class="verify-icon">📧</div>
        <h1>Verify Your Email</h1>
        <p class="security-description">
            Please check your email and click the verification link to activate your account. 
            If you didn't receive the email, you can request a new one below.
        </p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="verify-box">
            ✓ A verification link has been sent to your email address. Please check your inbox (and spam folder) and click the link to complete your registration.
        </div>

        <div class="button-group">
            <form method="POST" action="/email/resend">
                @csrf
                <button type="submit" class="security-btn">Resend Verification Email</button>
            </form>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="security-btn security-btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
