<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [\App\Http\Controllers\API\AuthController::class, 'register'])->name('api.register');
Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'login'])->name('api.login');

Route::group(['middleware' => 'api.jwt'], function () {
    Route::get('me', [\App\Http\Controllers\API\AuthController::class, 'me']);
    Route::post('logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
    Route::post('refresh', [\App\Http\Controllers\API\AuthController::class, 'refresh']);

    Route::apiResource('categories', \App\Http\Controllers\API\CategoryController::class);
    Route::apiResource('books', \App\Http\Controllers\API\BookController::class);
    Route::apiResource('members', \App\Http\Controllers\API\MemberController::class);
    Route::apiResource('loans', \App\Http\Controllers\API\LoanController::class);
});
