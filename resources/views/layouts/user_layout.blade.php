<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
    @vite('resources/css/style.css')
</head>
<body>
    <div class="main-container">
        <div class="first-container">
            <div class="logo-box">
                <span class="egg">
                    <img src="/images/eggleft.png" id="e-left">
                    <img src="/images/chick.png" id="chick">
                    <img src="/images/eggright.png" id="e-right">
                </span>
                <span class="title-logo">
                    <div class="egg-crack">
                        <img src="/images/eggleft.png" id="egg-crack-left">
                        <img src="/images/eggright.png" id="egg-crack-right">
                    </div>
                    <h1>myBL&nbsp;&nbsp;G</h1>
                    <img src="/images/yolk.png" id="yolk">
                    <p>The Daily Eggspress</p>
                </span>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Search...">
                <button class="menu-btn">
                    <img src="/images/search.png" id="search-icn">
                </button>
            </div>
            <div class="sign-box user-sign-box">
                <button type="button" class="profile-menu-btn" aria-expanded="false">
                    <img src="{{ Auth::user() && Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : '/images/user.png' }}" class="profile-pic" alt="Profile">
                    <img src="/images/right.png" class="profile-arrow" alt="Menu">
                </button>
                <div class="profile-dropdown" aria-hidden="true">
                    <a href="{{ route('user.profile') }}" class="profile-dd-item">Profile</a>
                    <a href="/user/dashboard" class="profile-dd-item">Go Home</a>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="profile-dd-item profile-dd-btn">Logout</button>
                    </form>
                </div>
                <button class="condensed-sign" type="button"><img src="{{ Auth::user() && Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : '/images/user.png' }}" id="user-icn"></button>
            </div>
            <div class="condensed-sign-window user-condensed-sign-window">
                <div class="cs-2 user-mobile-actions">
                    <a href="{{ route('user.profile') }}" class="mobile-user-link">Profile</a>
                    <a href="/user/dashboard" class="mobile-user-link">Go Home</a>
                    <form action="/logout" method="POST" class="mobile-logout-form">
                        @csrf
                        <button type="submit" id="cs-signout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>
        @yield('content')
    </div>
    @vite('resources/js/script.js')
    @stack('scripts')
</body>
</html>
