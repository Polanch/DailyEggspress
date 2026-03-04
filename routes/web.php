<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;

Route::get('/', [BlogController::class, 'showHome']);
Route::get('/login', [LoginController::class, 'showLogin']);

Route::get('/home', [LoginController::class, 'showLogin'])->name('login');
Route::get('/blogs/{id}', [BlogController::class, 'showPublicBlog'])->name('blogs.view');
Route::get('/tags/{tag}', [BlogController::class, 'showTagBlogs'])->name('tags.show');


Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/banned/appeal', [LoginController::class, 'showBannedAppeal'])->middleware('auth')->name('banned.appeal');
Route::post('/banned/appeal', [LoginController::class, 'submitBannedAppeal'])->middleware('auth')->name('banned.appeal.submit');

Route::get('/user/dashboard', [LoginController::class, 'showUserDashboard'])->middleware('auth');
Route::get('/user/home', [BlogController::class, 'showUserHome'])->middleware(['auth', 'verified'])->name('user.home');
Route::get('/user/profile', [ProfileController::class, 'show'])->middleware(['auth', 'verified'])->name('user.profile');
Route::patch('/user/profile', [ProfileController::class, 'update'])->middleware(['auth', 'verified'])->name('user.profile.update');
Route::get('/user/blogs/{id}', [BlogController::class, 'showUserBlog'])->middleware(['auth', 'verified'])->name('user.blog.view');
Route::post('/user/blogs/{id}/reaction', [BlogController::class, 'saveReaction'])->middleware(['auth', 'verified'])->name('user.blog.reaction');
Route::post('/user/blogs/{id}/comment', [BlogController::class, 'saveComment'])->middleware(['auth', 'verified'])->name('user.blog.comment');

// Admin and Moderator dashboard and moderation routes (with some restrictions for moderator)
Route::middleware('checkRole:admin,moderator')->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'showDashboard'])->name('admin.dashboard');
    Route::get('/admin/posts', [BlogController::class, 'showAdminPosts'])->name('admin.posts');
    Route::get('/admin/comments', [BlogController::class, 'showAdminComments'])->name('admin.comments');
    Route::get('/admin/comments/search', [BlogController::class, 'searchAdminComments'])->name('admin.comments.search');
    Route::delete('/admin/comments/{id}', [BlogController::class, 'deleteAdminComment'])->name('admin.comments.delete');
    Route::get('/admin/workspace', [BlogController::class, 'index']);
    Route::get('/admin/workspace/create', [BlogController::class, 'showDrafts'])->middleware('auth');
    Route::get('/admin/users', [BlogController::class, 'showAdminUsers'])->name('admin.users');
    Route::get('/admin/users/search', [BlogController::class, 'searchAdminUsers'])->name('admin.users.search');
    Route::get('/admin/users/{id}/appeal', [BlogController::class, 'showUserAppeal'])->name('admin.users.appeal');
    Route::post('/admin/users/{id}/ban', [BlogController::class, 'banUser'])->name('admin.users.ban');
    Route::post('/admin/users/{id}/unban', [BlogController::class, 'unbanUser'])->name('admin.users.unban');
    Route::post('/admin/comments/{id}/ban', [BlogController::class, 'banCommentUser'])->name('admin.comments.ban');
});

// Admin-only routes (trash management and user deletion)
Route::middleware('checkRole:admin')->group(function () {
    Route::get('/admin/trash', [BlogController::class, 'showAdminTrash'])->name('admin.trash');
    Route::get('/admin/trash/search', [BlogController::class, 'searchAdminTrash'])->name('admin.trash.search');
    Route::post('/admin/trash/{id}/restore', [BlogController::class, 'restoreFromTrash'])->name('admin.trash.restore');
    Route::delete('/admin/trash/{id}', [BlogController::class, 'deleteTrashBlog'])->name('admin.trash.delete');
    Route::delete('/admin/users/{id}', [BlogController::class, 'deleteUser'])->name('admin.users.delete');
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

Route::post('/admin/blogs', [BlogController::class, 'store'])->middleware('checkRole:admin,moderator')->name('blogs.store');
Route::get('/admin/blogs/{id}', [BlogController::class, 'show'])->middleware('checkRole:admin,moderator')->name('blogs.show');
Route::patch('/admin/blogs/{id}', [BlogController::class, 'update'])->middleware('checkRole:admin,moderator')->name('blogs.update');
Route::patch('/admin/blogs/{id}/reschedule', [BlogController::class, 'reschedule'])->middleware('checkRole:admin,moderator')->name('blogs.reschedule');
Route::delete('/blogs/{id}', [BlogController::class, 'destroy'])->middleware('checkRole:admin,moderator')->name('blogs.destroy');
Route::post('/blogs/{id}/trash', [BlogController::class, 'moveToTrash'])->middleware('checkRole:admin,moderator')->name('blogs.trash');

Route::post('/blog-image-upload', [BlogController::class, 'uploadImage'])->middleware('checkRole:admin,moderator')->name('blog.image.upload');
Route::post('/remove-image', [BlogController::class, 'removeImage'])->middleware('checkRole:admin,moderator')->name('remove-image');

Route::get('/forgot-password', [App\Http\Controllers\LoginController::class, 'showForgotPassword'])->name('password.forgot.form');
Route::post('/forgot-password/send-otp', [App\Http\Controllers\LoginController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/forgot-password/verify-otp', [App\Http\Controllers\LoginController::class, 'showVerifyOtp'])->name('password.otp.verify.form');
Route::post('/forgot-password/verify-otp', [App\Http\Controllers\LoginController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/forgot-password/reset', [App\Http\Controllers\LoginController::class, 'showResetPassword'])->name('password.reset.form');
Route::post('/forgot-password/reset', [App\Http\Controllers\LoginController::class, 'resetPassword'])->name('password.reset');