@extends('layouts.app')

@section('title', 'Рейтинг игроков')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-trophy text-warning"></i> Рейтинг игроков</h1>
</div>

<!-- Поиск и фильтрация -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('rating.search') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" 
                       placeholder="Поиск по имени или логину..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="game" class="form-select">
                    <option value="">Все игры</option>
                    <option value="sudoku" {{ request('game') == 'sudoku' ? 'selected' : '' }}>Судоку</option>
                    <option value="memory" {{ request('game') == 'memory' ? 'selected' : '' }}>Найди пару</option>
                    <option value="snake" {{ request('game') == 'snake' ? 'selected' : '' }}>Змейка</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Поиск
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Таблица рейтинга -->
@if($users->isEmpty())
    <div class="alert alert-info">Пользователи пока не играли в игры.</div>
@else
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                    <th>Всего очков</th>
                    <th>Судоку</th>
                    <th>Найди пару</th>
                    <th>Змейка</th>
                    <th>Игр сыграно</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="Avatar" 
                                         style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                                @else
                                    <i class="fas fa-user-circle fa-2x me-2"></i>
                                @endif
                                {{ $user->imya ?? $user->login }}
                            </div>
                        </td>
                        <td><strong>{{ $user->total_points ?? 0 }}</strong></td>
                        <td>{{ $user->sudoku_points ?? 0 }}</td>
                        <td>{{ $user->memory_points ?? 0 }}</td>
                        <td>{{ $user->snake_points ?? 0 }}</td>
                        <td>{{ $user->games_played ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endif
@endsection