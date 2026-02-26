<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;


class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/user/dashboard');
        }
        return view('welcome');
    }

    public function login(Request $request)
    {
        $login = $request->input('email');
        $password = $request->input('password');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $remember = $request->filled('remember');
        if (Auth::attempt([$fieldType => $login, 'password' => $password], $remember)) {
            $user = Auth::user();
            Session::put('role', $user->role);
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/user/dashboard');
        }
        return redirect('/')->with('error', 'Invalid credentials.');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
     public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'birthday' => 'required|date',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        try {
            $user = new User();
            $user->first_name = $validated['first_name'];
            $user->last_name = $validated['last_name'];
            $user->username = $validated['username'];
            $user->birthday = $validated['birthday'];
            $user->email = $validated['email'];
            $user->password = bcrypt($validated['password']);
            $user->role = 'user';
            $user->save();

            Auth::login($user);
            $user->sendEmailVerificationNotification();
            return redirect('/email/verify')->with('success', 'Registration successful! Please verify your email.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Registration failed. Please try again.');
        }
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect('/')->with('error', 'Invalid verification link.');
        }
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return redirect('/user/dashboard')->with('success', 'Email verified!');
    }

    public function resendVerification(Request $request)
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            return redirect('/user/dashboard');
        }
        $user->sendEmailVerificationNotification();
        return back()->with('success', 'Verification email resent!');
    }

    public function showUserDashboard()
    {
        $user = Auth::user();
        if (! $user->hasVerifiedEmail()) {
            return redirect('/email/verify')->with('error', 'Please verify your email before accessing the dashboard.');
        }
        return view('user_dashboard');
    }
}