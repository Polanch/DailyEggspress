<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="/images/browser-logo.png">
    <title>Daily Eggspress</title>
    @vite('resources/css/welcome_style.css')
    <script>
        window.showRegister = {{ (session('show_register') || request()->query('form') === 'register') ? 'true' : 'false' }};
    </script>
</head>
<body>
    <div class="main-container">
        <div class="the-window">
            <div class="egg-holder">
                <div class="egg-box">
                    <img src="/images/eggleft.png" id="shell-top">
                    <img src="/images/eggright.png" id="shell-bottom">
                    <div id="chick"></div>
                </div>
            </div>
            <div class="text-box">
                <h1>myBL<img src="/images/yolk2.png" id="yolk">G</h1>
            </div>
            <div class="p-box">
                <p>the Daily Eggspress</p>
            </div>
            <div class="tagline">
                <p>"Fresh Thoughts Delivered Daily"</p>
            </div>
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <img src="/images/feather.png" alt="Feather">
            <div class="footer">
                <p>All Rights Reserved &copy;</p>
                <p>Developed by John Lloyd Olipani</p>
            </div>
        </div>
        <div class="the-forms">
            <div class="form-slider">
                <form class="login-form" action="/login" method="post" @if(session('show_register')) style="display:none;" @endif>
                    <h1>Welcome to the Coop</h1>
                    @csrf
                    <label for="login-email">Email or Username</label>
                    <div class="input-icon">
                        <span class="icon-envelope"><img src="/images/l1.png" class="log-icn"></span>
                        <input id="login-email" type="text" name="email" placeholder="Enter your email or username" required>
                    </div>
                    <label for="login-password">Password</label>
                    <div class="input-icon">
                        <span class="icon-lock"><img src="/images/l2.png" class="log-icn"></span>
                        <input class="login-password" id="login-password" type="password" name="password" placeholder="Enter your password" required>
                        <span class="toggle-password" style="cursor:pointer;">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;display:inline;"><path stroke="#9C6D55" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3" stroke="#9C6D55" stroke-width="2"/></svg>
                            <svg class="eye-closed-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;display:none;"><path stroke="#9C6D55" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3" stroke="#9C6D55" stroke-width="2"/><line x1="4" y1="20" x2="20" y2="4" stroke="#9C6D55" stroke-width="2"/></svg>
                        </span>
                    </div>
                    <div class="form-row" style="margin-bottom: 10px; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center;">
                            <input type="checkbox" id="remember" name="remember" style="width:16px;height:16px;margin-right:6px;">
                            <label for="remember" style="font-size:0.98rem; color:#23130B; cursor:pointer;">Keep me logged in</label>
                        </div>
                        <a href="{{ route('password.forgot.form') }}" style="font-size:0.98rem; color:#9C6D55; text-decoration:none; margin-left:10px;">Forgot password?</a>
                    </div>
                    @if(session('error'))
                        <div class="form-error">{{ session('error') }}</div>
                    @endif
                    <button type="submit">Login</button>
                    <div class="form-link">
                        <a href="#" id="show-register">Don't have an account? Create Account</a>
                    </div>
                </form>
                <!-- Register Form -->
                <form class="register-form" action="/register" method="post" @if(session('show_register')) style="display:flex;" @else style="display:none;" @endif>
                    @csrf
                    <h1>Incubator</h1>
                    <label for="reg-username">Username</label>
                    <div class="input-icon">
                        <input id="reg-username" type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <label for="reg-first-name">First Name</label>
                            <div class="input-icon">
                                <input id="reg-first-name" type="text" name="first_name" placeholder="First Name" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <label for="reg-last-name">Last Name</label>
                            <div class="input-icon">
                                <input id="reg-last-name" type="text" name="last_name" placeholder="Last Name" required>
                            </div>
                        </div>
                    </div>
                    <label for="reg-birthday">Birthday</label>
                    <div class="input-icon">
                        <input id="reg-birthday" type="date" name="birthday" required style="padding-right: 15px;">
                    </div>
                    <label for="reg-email">Email</label>
                    <div class="input-icon">
                        <input id="reg-email" type="email" name="email" placeholder="Email" required>
                    </div>
                    <label for="reg-password">Password</label>
                    <div class="input-icon">
                        <input class="login-password" id="reg-password" type="password" name="password" placeholder="Password" required>
                        <span class="toggle-password" style="cursor:pointer;">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;display:inline;"><path stroke="#9C6D55" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3" stroke="#9C6D55" stroke-width="2"/></svg>
                            <svg class="eye-closed-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;display:none;"><path stroke="#9C6D55" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3" stroke="#9C6D55" stroke-width="2"/><line x1="4" y1="20" x2="20" y2="4" stroke="#9C6D55" stroke-width="2"/></svg>
                        </span>
                    </div>
                    <label for="reg-password-confirm">Confirm Password</label>
                    <div class="input-icon">
                        <input class="login-password" id="reg-password-confirm" type="password" name="password_confirmation" placeholder="Confirm Password" required>
                        <span class="toggle-password" style="cursor:pointer;">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;display:inline;"><path stroke="#9C6D55" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3" stroke="#9C6D55" stroke-width="2"/></svg>
                            <svg class="eye-closed-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" style="vertical-align:middle;display:none;"><path stroke="#9C6D55" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3" stroke="#9C6D55" stroke-width="2"/><line x1="4" y1="20" x2="20" y2="4" stroke="#9C6D55" stroke-width="2"/></svg>
                        </span>
                    </div>
                    <input type="hidden" name="role" value="user">
                    @if ($errors->any())
                        <div class="form-error">
                            <ul style="margin:0;padding:0;list-style:none;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="form-error">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="form-success">{{ session('success') }}</div>
                    @endif
                    <button type="submit">Create Account</button>
                    <div class="form-link">
                        <a href="#" id="show-login">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="new-footer">
            <p>All Rights Reserved &copy;</p>
            <p>Developed by John Lloyd Olipani</p>
        </div>
    </div>
    @vite('resources/js/welcome_script.js')
</body>
</html>