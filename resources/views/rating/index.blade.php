@extends('layouts.app')

@section('title', 'Рейтинг игроков')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="rating-container">
            <!-- Заголовок -->
            <h1 class="rating-title">🏆 Рейтинг игроков</h1>
            
            <!-- ========================================== -->
            <!-- ПОИСК И ФИЛЬТРАЦИЯ                         -->
            <!-- ========================================== -->
            <div class="search-box">
                <form action="{{ route('rating.search') }}" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="search" class="search-input" 
                               placeholder="Поиск по имени или логину..." 
                               value="{{ request('search') }}">
                        
                        <select name="game" class="search-select">
                            <option value="">Все игры</option>
                            <option value="sudoku" {{ request('game') == 'sudoku' ? 'selected' : '' }}>🧩 Судоку</option>
                            <option value="memory" {{ request('game') == 'memory' ? 'selected' : '' }}>🃏 Найди пару</option>
                            <option value="snake" {{ request('game') == 'snake' ? 'selected' : '' }}>🐍 Змейка</option>
                        </select>
                    </div>
                    
                    <div class="search-actions">
                        <button type="submit" class="btn btn-primary btn-search">
                            <i class="fas fa-search me-1"></i>Поиск
                        </button>
                        @if(request('search') || request('game'))
                            <a href="{{ route('rating.index') }}" class="btn btn-secondary btn-reset">
                                <i class="fas fa-times me-1"></i>Сбросить
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            <!-- ========================================== -->
            <!-- ТАБЛИЦА РЕЙТИНГА                          -->
            <!-- ========================================== -->
            @if($users->isEmpty())
                <div class="no-results">
                    <i class="fas fa-users-slash"></i>
                    <p>Пользователи пока не играли в игры</p>
                </div>
            @else
                <div class="rating-table-container">
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Место</th>
                                <th>Игрок</th>
                                <th style="width: 100px;">Всего очков</th>
                                <th style="width: 90px;">🧩 Судоку</th>
                                <th style="width: 90px;">🃏 Найди пару</th>
                                <th style="width: 90px;">🐍 Змейка</th>
                                <th style="width: 80px;">Игр</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                @php
                                    $total = ($user->sudoku_points ?? 0) + ($user->memory_points ?? 0) + ($user->snake_points ?? 0);
                                @endphp
                                <tr class="{{ auth()->check() && auth()->id() == $user->id ? 'current-user' : '' }}">
                                    <td>
                                        @if($loop->iteration == 1)
                                            <span class="rank-medal">🥇</span>
                                        @elseif($loop->iteration == 2)
                                            <span class="rank-medal">🥈</span>
                                        @elseif($loop->iteration == 3)
                                            <span class="rank-medal">🥉</span>
                                        @else
                                            <span class="rank-number">{{ $loop->iteration }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="player-info">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="{{ $user->login }}" class="avatar-small">
                                            @else
                                                <div class="avatar-small avatar-placeholder-small">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <div class="player-details">
                                                <span class="player-name">
                                                    {{ $user->imya ?? $user->login }}
                                                    @if($user->rol_id == 3)
                                                        <span class="user-role-badge admin">Админ</span>
                                                    @elseif($user->rol_id == 2)
                                                        <span class="user-role-badge moderator">Модератор</span>
                                                    @endif
                                                </span>
                                                <span class="player-login">@ {{ $user->login }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="score-total">{{ $total }}</td>
                                    <td>{{ $user->sudoku_points ?? 0 }}</td>
                                    <td>{{ $user->memory_points ?? 0 }}</td>
                                    <td>{{ $user->snake_points ?? 0 }}</td>
                                    <td>{{ $user->games_played ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- ========================================== -->
                <!-- ПАГИНАЦИЯ                                 -->
                <!-- ========================================== -->
                <div class="rating-pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection