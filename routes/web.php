<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BlogController;

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::get('/login', [LoginController::class, 'showLogin']);

Route::get('/home', function () {
    return view('main');
});


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