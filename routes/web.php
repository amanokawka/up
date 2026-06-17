<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', [PageController::class, 'index'])->name('home');

// Аутентификация
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Страницы игр
Route::get('/games/sudoku', [GameController::class, 'sudoku'])->name('games.sudoku');
Route::get('/games/memory', [GameController::class, 'memory'])->name('games.memory');
Route::get('/games/snake', [GameController::class, 'snake'])->name('games.snake');

// Рейтинг
Route::get('/rating', [RatingController::class, 'index'])->name('rating.index');
Route::get('/rating/search', [RatingController::class, 'search'])->name('rating.search');

// Поддержка
Route::middleware(['auth'])->group(function () {
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/message', [SupportController::class, 'message'])->name('support.message');
});

// Профиль
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
});

// Админ-панель
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/users/search', [AdminUserController::class, 'search'])->name('admin.users.search');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('admin.users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('admin.users.unban');
    Route::post('/users/{user}/reset-stats', [AdminUserController::class, 'resetStats'])->name('admin.users.reset-stats');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{user}/role', [AdminUserController::class, 'changeRole'])->name('admin.users.role');
});

// Удаляем require __DIR__.'/auth.php'; если он есть