@extends('layouts.app')

@section('title', 'Главная')

@section('content')
<div class="text-center mb-5">
    <h1 class="page-title">🌿 Добро пожаловать в Мини-Игры!</h1>
    <p class="page-subtitle">Выберите игру и наслаждайтесь!</p>
</div>

<div class="games-grid">
    <!-- Судоку -->
    <div class="game-card">
        <div class="icon-wrapper icon-sudoku">
            <i class="fas fa-th"></i>
        </div>
        <h3>Судоку</h3>
        <p>Классическая головоломка с цифрами. Развивайте логику!</p>
        <a href="{{ route('games.sudoku') }}" class="btn btn-primary">
            <i class="fas fa-play me-2"></i>Играть
        </a>
    </div>

    <!-- Найди пару -->
    <div class="game-card">
        <div class="icon-wrapper icon-memory">
            <i class="fas fa-images"></i>
        </div>
        <h3>Найди пару</h3>
        <p>Тренируйте память, находя одинаковые картинки!</p>
        <a href="{{ route('games.memory') }}" class="btn btn-success">
            <i class="fas fa-play me-2"></i>Играть
        </a>
    </div>

    <!-- Змейка -->
    <div class="game-card">
        <div class="icon-wrapper icon-snake">
            <i class="fas fa-dragon"></i>
        </div>
        <h3>Змейка</h3>
        <p>Классическая аркада. Собирайте еду и растите!</p>
        <a href="{{ route('games.snake') }}" class="btn btn-warning">
            <i class="fas fa-play me-2"></i>Играть
        </a>
    </div>
</div>
@endsection