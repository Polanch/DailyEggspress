<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" href="/images/browser-logo.png">
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
            <div class="sign-box">
                <button type="button" class="sign-btn" onclick="window.location.href='{{ url('/login') }}'">Sign In</button>
                <button type="button" class="sign-btn" onclick="window.location.href='{{ url('/login?form=register') }}'">Register</button>
                <button class="condensed-sign"><img src="/images/user.png" id="user-icn"></button>
            </div>
            <div class="condensed-sign-window">
                <div class="cs-1">
                    <p>Do you want to get in touch with me?</p>
                    <button id="cs-portfolio-btn"><img src="images/portfolio.png" id="portfolio-icn"> Visit my Portfolio</button>
                </div>
                <div class="cs-2">
                    <p>Don't have an account?</p>
                    <button id="cs-register-btn" type="button" onclick="window.location.href='{{ url('/login?form=register') }}'">Register</button>
                    <button id="cs-signin-btn" type="button" onclick="window.location.href='{{ url('/login') }}'">Sign In</button>
                </div>
            </div>
        </div>
        @yield('content')
    </div>
    @vite('resources/js/script.js')
    @stack('scripts')
</body>
</html>