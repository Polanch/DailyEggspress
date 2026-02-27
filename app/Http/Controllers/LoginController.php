<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'birthday' => 'required|date',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);
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
            return redirect('/')->withInput()->withErrors($e->validator)->with('show_register', true);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Registration failed. Please try again.')->with('show_register', true);
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

    // Show form to request OTP
    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    // Handle OTP request and send email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $otp = random_int(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(30);
        DB::table('password_resets_otp')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        // Send OTP email
        Mail::raw("Your password reset OTP is: $otp\nIt expires in 30 minutes.", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Your Password Reset OTP');
        });
        return redirect()->route('password.otp.verify.form')->with(['email' => $request->email, 'success' => 'OTP sent to your email.']);
    }

    // Show form to enter OTP
    public function showVerifyOtp(Request $request)
    {
        $email = session('email', $request->email);
        return view('auth.verify_otp', compact('email'));
    }

    // Handle OTP verification
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);
        $record = DB::table('password_resets_otp')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();
        if (!$record || Carbon::parse($record->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }
        // OTP is valid, allow password reset
        session(['reset_email' => $request->email]);
        return redirect()->route('password.reset.form');
    }

    // Show form to reset password
    public function showResetPassword(Request $request)
    {
        $email = session('reset_email');
        if (!$email) return redirect()->route('password.forgot.form');
        return view('auth.reset_password', compact('email'));
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();
        // Clean up OTP
        DB::table('password_resets_otp')->where('email', $request->email)->delete();
        session()->forget('reset_email');
        return redirect('/')->with('success', 'Password reset successful! You can now log in.');
    }
}