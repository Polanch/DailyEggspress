<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/images/browser-logo.png">
    <title>Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/admin_style.css')
</head>
<body>
    <div class="side-nav">
        <div class="logo-container">
            <div class="logo-box">
                <span class="egg">
                    <img src="/images/eggleft.png" id="e-left">
                    <img src="/images/adminchick.png" id="chick">
                    <img src="/images/eggright.png" id="e-right">
                </span>
                <span class="title-logo">
                    <div class="egg-crack">
                        <img src="/images/eggleft.png" id="egg-crack-left">
                        <img src="/images/eggright.png" id="egg-crack-right">
                    </div>
                    <h1>myBL&nbsp;&nbsp;G</h1>
                    <img src="/images/yolk.png" id="yolk">
                    <p>@if(Auth::user()->role === 'admin') Eggministrator @else Moderator @endif</p>
                </span>
            </div>
        </div>
        <div class="profile-container">
            <img src="{{ Auth::user()->banner ? asset(Auth::user()->banner) : '/thumbnails/something4.jpg' }}" class="profile-bg">
            <div class="profile-box">
                <h3>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h3>
                <p>{{ ucfirst(Auth::user()->role) }}</p>
                <button class="edit-profile" onclick="navigateToProfile()">Edit Profile</button>
            </div>
            <img src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : '/images/empty.png' }}" id="pfpic">
        </div>
        <div class="menu-container">
            <ul class="admin-menu">
                <li>
                    <a href="/admin/dashboard" class="active">
                        <img src="/images/menu1.png" class="admin-menu-icn">
                        <p>Dashboard</p>
                    </a>
                </li>
                <li>
                    <a href="/admin/workspace">
                        <img src="/images/menu2.png" class="admin-menu-icn">
                        <p>Workspace</p>
                    </a>
                </li>
                <li>
                    <a href="/admin/comments">
                        <img src="/images/menu3.png" class="admin-menu-icn">
                        <p>Comments</p>
                    </a>
                </li>
                <li>
                    <a href="/admin/posts">
                        <img src="/images/menu4.png" class="admin-menu-icn">
                        <p>Posts</p>
                    </a>
                </li>
                @if(Auth::user()->role === 'admin')
                <li>
                    <a href="/admin/trash">
                        <img src="/images/menu5.png" class="admin-menu-icn">
                        <p>Trash</p>
                    </a>
                </li>
                @endif
                <li>
                    <a href="/admin/users">
                        <img src="/images/menu6.png" class="admin-menu-icn">
                        <p>Users</p>
                    </a>
                </li>
            </ul>
        </div>
        <p class="brand">All Rights Reserved &copy;</p>
    </div>
    <div class="main-content">
        <div class="top-nav">
            <button class="menu-btn" aria-label="Toggle menu">
                <div class="burger">
                    <span class="mb"></span>
                    <span class="mb"></span>
                    <span class="mb"></span>
                    <span class="mb"></span>
                </div>
                <h2>Menu</h2>
            </button>
            
            <div class="top-menu">
                <a class="top-btn t1" href="/user/dashboard" aria-label="View Website">
                    <img src="/images/top1.png" class="top-icn">
                    <span class="btn-text">View Website</span>
                </a>
                <a class="top-btn t2" href="#" aria-label="Sign Out" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                    <img src="/images/top2.png" class="top-icn">
                    <span class="btn-text">Sign Out</span>
                </a>
                <form id="admin-logout-form" action="/logout" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
        <div class="content-body">
            @yield('content')
        </div>
    </div>
    @vite('resources/js/admin_script.js')
    <script>
        function navigateToProfile() {
            window.location.href = '{{ route("user.profile") }}';
        }
    </script>
</body>
</html>