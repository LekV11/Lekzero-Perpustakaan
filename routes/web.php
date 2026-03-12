<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookWebController;
use App\Http\Controllers\MemberWebController;
use App\Http\Controllers\CategoryWebController;
use App\Http\Controllers\LoanWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// authentication pages
Route::get('login', [\App\Http\Controllers\WebAuthController::class, 'showLogin'])->name('login');
Route::post('login', [\App\Http\Controllers\WebAuthController::class, 'login'])->name('login.post');
Route::get('register', [\App\Http\Controllers\WebAuthController::class, 'showRegister'])->name('register');
Route::post('register', [\App\Http\Controllers\WebAuthController::class, 'register'])->name('register.post');
Route::post('logout', [\App\Http\Controllers\WebAuthController::class, 'logout'])->name('logout');

// protected routes
Route::middleware('jwt.session')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('books', BookWebController::class)
        ->except(['create','store','edit','update','destroy'])
        ->names('books');

    Route::middleware('admin')->group(function () {
        Route::resource('books', BookWebController::class)
            ->only(['create','store','edit','update','destroy']);
        Route::resource('categories', CategoryWebController::class);
        Route::resource('members', MemberWebController::class);
        Route::resource('loans', LoanWebController::class);
    });
});
