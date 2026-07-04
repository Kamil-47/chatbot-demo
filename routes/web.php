<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

// Strona powitalna (tylko dla niezalogowanych)
Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome')->middleware('guest');

// Logowanie i rejestracja (tylko dla niezalogowanych)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/login-as-demo', [LoginController::class, 'loginAsDemo'])->name('login.demo');

    // Rejestracja wyłączona w trybie demo
    if (!config('app.demo_mode')) {
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
    }
});

// Wylogowanie (dla zalogowanych)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Panel dla zwykłych użytkowników (zalogowani, ale nie admini)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// Trasy dla adminów (zabezpieczone middleware auth i isAdmin)
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('student', StudentController::class);
    Route::resource('lesson', LessonController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('payment', PaymentController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('/lesson/generate', [LessonController::class, 'generate'])->name('lesson.generate');
    Route::post('/payment/{id}/mark-paid', [PaymentController::class, 'markPaid'])->name('payment.mark-paid');
    Route::get('/prompt/edit', [PromptController::class, 'edit'])->name('prompt.edit');
    Route::put('/prompt', [PromptController::class, 'update'])->name('prompt.update');
    Route::post('/api/chat', [ChatBotController::class, 'chat'])
        ->middleware('throttle:20,60')
        ->name('api.chat');
});