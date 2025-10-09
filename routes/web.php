<?php

use App\Http\Controllers\ContestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Homepage - Shows Golden Question (Sticker QR destination)
Route::get('/', [ContestController::class, 'show'])->name('home');

// Legal Pages
Route::get('/terms', fn() => Inertia::render('Legal/Terms'))->name('terms');
Route::get('/privacy', fn() => Inertia::render('Legal/Privacy'))->name('privacy');

// QR Code Scan Routes
Route::match(['get', 'post'], '/scan/{code}', [ScanController::class, 'scan'])->name('scan');
Route::get('/stickers/{sticker}', [ScanController::class, 'show'])->name('stickers.show');

// Contest Routes
Route::middleware(['auth', 'age_verified', 'contest_active'])->group(function () {
    // Golden Question
    Route::get('/contest', [ContestController::class, 'show'])->name('contest.show');
    Route::post('/contest/submit', [ContestController::class, 'submit'])->name('contest.submit');
    Route::get('/contest/winner/{submission}', [ContestController::class, 'winner'])->name('contest.winner');
    Route::get('/contest/results/{question}', [ContestController::class, 'results'])->name('contest.results');
});

// User Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/winnings', [DashboardController::class, 'winnings'])->name('dashboard.winnings');
    Route::get('/dashboard/gift-cards', [DashboardController::class, 'giftCards'])->name('dashboard.gift-cards');
    Route::get('/dashboard/submissions', [DashboardController::class, 'submissions'])->name('dashboard.submissions');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
