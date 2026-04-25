<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookWebController;
use App\Http\Controllers\MemberWebController;
use App\Http\Controllers\CategoryWebController;
use App\Http\Controllers\LoanWebController;

Route::get('login', [\App\Http\Controllers\WebAuthController::class, 'showLogin'])->name('login');
Route::post('login', [\App\Http\Controllers\WebAuthController::class, 'login'])->name('login.post');
Route::get('register', [\App\Http\Controllers\WebAuthController::class, 'showRegister'])->name('register');
Route::post('register', [\App\Http\Controllers\WebAuthController::class, 'register'])->name('register.post');
Route::post('logout', [\App\Http\Controllers\WebAuthController::class, 'logout'])->name('logout');

// Google Auth Routes
Route::get('auth/google', [\App\Http\Controllers\WebAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [\App\Http\Controllers\WebAuthController::class, 'handleGoogleCallback']);

Route::middleware('jwt.session')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('books', BookWebController::class);

    Route::middleware('admin')->group(function () {
        Route::resource('categories', CategoryWebController::class);
        Route::resource('members', MemberWebController::class);
        Route::resource('loans', LoanWebController::class);
    });
});
