@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-shield-alt text-primary"></i> Админ-панель</h1>
    <span class="badge bg-primary">Добро пожаловать, {{ Auth::user()->imya ?? Auth::user()->login }}!</span>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-users"></i> Пользователи
                </h5>
                <h2>{{ $stats['total_users'] }}</h2>
                <p class="mb-0">Активных пользователей</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-success text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-ticket-alt"></i> Открытые тикеты
                </h5>
                <h2>{{ $stats['open_tickets'] }}</h2>
                <p class="mb-0">Требуют внимания</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-warning text-dark mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-gamepad"></i> Всего игр сыграно
                </h5>
                <h2>{{ $stats['total_games'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-ban"></i> Заблокировано
                </h5>
                <h2>{{ $stats['banned_users'] }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-secondary text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-trophy"></i> Топ-игрок
                </h5>
                <h6>{{ $stats['top_player'] ?? 'Нет данных' }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Быстрые действия -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users') }}" class="btn btn-primary">
                        <i class="fas fa-users-cog"></i> Управление пользователями
                    </a>
                    <a href="{{ route('support.index') }}" class="btn btn-warning">
                        <i class="fas fa-headset"></i> Перейти в поддержку
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Последние тикеты</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_tickets']->isEmpty())
                    <p class="text-muted">Нет новых тикетов</p>
                @else
                    @foreach($stats['recent_tickets'] as $ticket)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span>{{ $ticket->tema }}</span>
                            <span class="badge bg-{{ $ticket->status == 'open' ? 'danger' : ($ticket->status == 'in_progress' ? 'warning' : 'success') }}">
                                {{ $ticket->status }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection