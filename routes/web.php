<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('main');
});

Route::get('/admin/dashboard', function () {
    return view('admin_dashboard');
});

Route::get('/admin/workspace', function () {
    return view('admin_workspace');
});

Route::get('/admin/workspace/create', function () {
    return view('admin_workspace_createblog');
});
