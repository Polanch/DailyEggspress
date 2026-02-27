<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BlogController;

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::get('/login', [LoginController::class, 'showLogin']);

Route::get('/home', [BlogController::class, 'showHome']);


Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/user/dashboard', [LoginController::class, 'showUserDashboard'])->middleware('auth');

Route::middleware('checkRole:admin')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin_dashboard');
    });
    Route::get('/admin/workspace', function () {
        return view('admin_workspace');
    });
    Route::get('/admin/workspace/create', function () {
        return view('admin_workspace_createblog');
    });
    Route::get('/admin/workspace/createtest', function () {
        return view('admin_workspace_createtest');
    });
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\LoginController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/resend', [App\Http\Controllers\LoginController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

Route::post('/admin/blogs', [BlogController::class, 'store'])->middleware('auth')->name('blogs.store');

Route::post('/blog-image-upload', [BlogController::class, 'uploadImage'])->middleware('checkRole:admin')->name('blog.image.upload');
Route::post('/remove-image', [BlogController::class, 'removeImage'])->middleware('checkRole:admin')->name('remove-image');

Route::get('/forgot-password', [App\Http\Controllers\LoginController::class, 'showForgotPassword'])->name('password.forgot.form');
Route::post('/forgot-password/send-otp', [App\Http\Controllers\LoginController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/forgot-password/verify-otp', [App\Http\Controllers\LoginController::class, 'showVerifyOtp'])->name('password.otp.verify.form');
Route::post('/forgot-password/verify-otp', [App\Http\Controllers\LoginController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/forgot-password/reset', [App\Http\Controllers\LoginController::class, 'showResetPassword'])->name('password.reset.form');
Route::post('/forgot-password/reset', [App\Http\Controllers\LoginController::class, 'resetPassword'])->name('password.reset');