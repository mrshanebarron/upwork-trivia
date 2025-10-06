<?php

use App\Http\Controllers\Admin\AdBoxController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TriviaCodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::middleware('throttle:60,1')->group(function () {
    Route::post('/api/lookup', [HomeController::class, 'lookup']);
    Route::post('/api/ad-click/{adBox}', [HomeController::class, 'trackAdClick']);
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/analytics/export', [DashboardController::class, 'exportAnalytics'])->name('analytics.export');
        Route::resource('trivia-codes', TriviaCodeController::class);
        Route::resource('ad-boxes', AdBoxController::class);
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
