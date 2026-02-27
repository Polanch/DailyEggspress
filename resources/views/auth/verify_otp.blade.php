@extends('layouts.security_layout')
@section('content')
<div class="otp-reset-container">
    <h2>Verify OTP</h2>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('password.otp.verify') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <label for="otp">Enter the 6-digit OTP sent to your email:</label>
        <input type="text" name="otp" id="otp" maxlength="6" required pattern="\d{6}">
        <button type="submit">Verify OTP</button>
    </form>
    <p>OTP is valid for 30 minutes.</p>
</div>
@endsection
