<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NewsFeedController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;

// User Authentication Routes
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Article Management Routes
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

    // User Preferences Settings
    Route::post('/preferences', [UserPreferenceController::class, 'setPreferences'])->name('set-preferences');
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences'])->name('get-preferences');

    // User Personalized News Feed
    Route::get('/personalized-feed', [NewsFeedController::class, 'getPersonalizedFeed'])->name('get-personalized-feed');
});
