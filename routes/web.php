<?php

use App\Http\Controllers\ContestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SimpleQuestionController;
use App\Http\Controllers\TriviaCodeController;
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

// Homepage - Shows Simple Questions (Default QR destination)
Route::get('/', [SimpleQuestionController::class, 'index'])->name('home');

// Bag Trivia - Shows Golden Question + Bag's trivia (Bag QR destination)
Route::get('/trivia', [TriviaCodeController::class, 'show'])->name('trivia.show');
Route::post('/trivia/submit', [TriviaCodeController::class, 'submitBagAnswer'])
    ->middleware('throttle:10,1')
    ->name('trivia.submit');

// Public Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// QR Code Scan Routes
Route::match(['get', 'post'], '/scan/{code}', [ScanController::class, 'scan'])->name('scan');
Route::get('/stickers/{sticker}', [ScanController::class, 'show'])->name('stickers.show');

// Contest Routes (Hidden Features - All Advanced Functionality)
Route::prefix('contest')->name('contest.')->group(function () {
    Route::middleware(['contest_active'])->group(function () {
        // Golden Question - Public view and submission
        Route::get('/', [ContestController::class, 'show'])->name('show');
        Route::post('/submit', [ContestController::class, 'submit'])
            ->middleware('throttle:10,1') // Max 10 submissions per minute per IP
            ->name('submit');

        // Winner page - requires authentication to claim prize
        Route::middleware(['auth', 'age_verified'])->group(function () {
            Route::get('/claim/{submission}', [ContestController::class, 'claim'])->name('claim');
        });

        // Results page - public
        Route::get('/results/{question}', [ContestController::class, 'results'])->name('results');
    });
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

    // Admin Routes
    Route::middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->group(function () {
        Route::get('/admin/import-questions', \App\Livewire\Admin\ImportQuestions::class)
            ->name('admin.import-questions');
    });
});

require __DIR__.'/auth.php';
