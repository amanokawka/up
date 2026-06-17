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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// ГЛАВНАЯ СТРАНИЦА
// ============================================
Route::get('/', [PageController::class, 'index'])->name('home');

// ============================================
// АУТЕНТИФИКАЦИЯ
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// СТРАНИЦЫ ИГР
// ============================================
Route::get('/games/sudoku', [GameController::class, 'sudoku'])->name('games.sudoku');
Route::get('/games/memory', [GameController::class, 'memory'])->name('games.memory');
Route::get('/games/snake', [GameController::class, 'snake'])->name('games.snake');

// ============================================
// СОХРАНЕНИЕ РЕЗУЛЬТАТОВ ИГР (только для авторизованных)
// ============================================
Route::post('/games/sudoku/save', [GameController::class, 'saveSudoku'])->name('games.sudoku.save')->middleware('auth');
Route::post('/games/memory/save', [GameController::class, 'saveMemory'])->name('games.memory.save')->middleware('auth');
Route::post('/games/snake/save', [GameController::class, 'saveSnake'])->name('games.snake.save')->middleware('auth');

// ============================================
// РЕЙТИНГ
// ============================================
Route::get('/rating', [RatingController::class, 'index'])->name('rating.index');
Route::get('/rating/search', [RatingController::class, 'search'])->name('rating.search');

// ============================================
// ПОДДЕРЖКА (только для авторизованных)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/message', [SupportController::class, 'message'])->name('support.message');
});

// ============================================
// ПРОФИЛЬ (только для авторизованных)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
});

// ============================================
// АДМИН-ПАНЕЛЬ (только для админов и модераторов)
// ============================================
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