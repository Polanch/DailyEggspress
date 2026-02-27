@extends('layouts.security_layout')
@section('content')
<div class="otp-reset-container">
    <h2>Forgot Password</h2>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('password.otp.send') }}">
        @csrf
        <label for="email">Enter your email address:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Send OTP</button>
    </form>
</div>
@endsection
