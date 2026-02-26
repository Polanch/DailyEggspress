@extends('layouts.layout')

@section('content')
<div class="container">
    <h1>Welcome to your User Dashboard!</h1>
    <p>You are logged in as a regular user.</p>
    <form action="/logout" method="POST" style="margin-top: 2rem;">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
@endsection
