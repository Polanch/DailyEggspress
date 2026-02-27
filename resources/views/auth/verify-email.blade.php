@extends('layouts.security_layout')
@section('content')
    <div class="main-container">
        <div class="the-window">
            <div class="text-box">
                <h1>Verify Your Email</h1>
            </div>
        </div>
        <div class="the-forms">
            <div class="form-slider">
                <h1>Email Verification</h1>
                <p>Please check your email and click the verification link to activate your account.</p>
                <form method="POST" action="/email/resend">
                    @csrf
                    <button type="submit">Resend Verification Email</button>
                </form>
                <form method="POST" action="/logout" style="margin-top: 20px;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
                @if(session('success'))
                    <div class="form-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="form-error">{{ session('error') }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
