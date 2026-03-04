<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="/images/browser-logo.png">
    <title>@yield('title', 'Security')</title>
    @vite('resources/css/security_pages.css')
    @yield('head')
</head>
<body>
    <div class="security-layout-container">
        @yield('content')
    </div>
</body>
</html>
