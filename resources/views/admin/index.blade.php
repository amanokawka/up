@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="admin-container">
            <!-- Заголовок -->
            <div class="admin-header">
                <div>
                    <h1 class="admin-title">⚙️ Админ-панель</h1>
                    <p class="admin-subtitle">Управление сайтом и пользователями</p>
                </div>
                <div class="admin-user-info">
                    <span class="badge admin-role-badge">
                        {{ Auth::user()->rol_id == 3 ? 'Администратор' : 'Модератор' }}
                    </span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- СТАТИСТИКА                                 -->
            <!-- ========================================== -->
            <div class="stats-grid-admin">
                <!-- Всего пользователей -->
                <div class="stat-card-admin">
                    <div class="stat-icon-admin">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['total_users'] ?? 0 }}</div>
                        <div class="stat-label">Всего пользователей</div>
                    </div>
                </div>

                <!-- Открытые тикеты -->
                <div class="stat-card-admin warning">
                    <div class="stat-icon-admin">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['open_tickets'] ?? 0 }}</div>
                        <div class="stat-label">Открытых тикетов</div>
                    </div>
                </div>

                <!-- Всего игр -->
                <div class="stat-card-admin success">
                    <div class="stat-icon-admin">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['total_games'] ?? 0 }}</div>
                        <div class="stat-label">Сыграно игр</div>
                    </div>
                </div>

                <!-- Заблокировано -->
                <div class="stat-card-admin danger">
                    <div class="stat-icon-admin">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['banned_users'] ?? 0 }}</div>
                        <div class="stat-label">Заблокированных</div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- БЫСТРЫЕ ДЕЙСТВИЯ                           -->
            <!-- ========================================== -->
            <div class="admin-actions">
                <h3>🚀 Быстрые действия</h3>
                <div class="admin-actions-grid">
                    <a href="{{ route('admin.users') }}" class="admin-action-btn primary">
                        <i class="fas fa-users-cog"></i>
                        <span>Управление пользователями</span>
                    </a>
                    <a href="{{ route('support.index') }}" class="admin-action-btn warning">
                        <i class="fas fa-headset"></i>
                        <span>Поддержка</span>
                        @if(($stats['open_tickets'] ?? 0) > 0)
                            <span class="badge bg-danger">{{ $stats['open_tickets'] }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ТОП-ИГРОК                                  -->
            <!-- ========================================== -->
            <div class="admin-top-player">
                <h3>🏆 Лучший игрок</h3>
                @if(isset($stats['top_player']) && $stats['top_player'])
                    <div class="top-player-card">
                        <div class="top-player-avatar">
                            @if($stats['top_player_avatar'])
                                <img src="{{ $stats['top_player_avatar'] }}" alt="">
                            @else
                                <i class="fas fa-user-circle"></i>
                            @endif
                        </div>
                        <div class="top-player-info">
                            <div class="top-player-name">{{ $stats['top_player'] }}</div>
                            <div class="top-player-stats">
                                <span>⭐ {{ $stats['top_player_score'] ?? 0 }} очков</span>
                            </div>
                        </div>
                        <div class="top-player-badge">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-trophy"></i>
                        <p>Пока нет данных</p>
                    </div>
                @endif
            </div>

            <!-- ========================================== -->
            <!-- ПОСЛЕДНИЕ ТИКЕТЫ                          -->
            <!-- ========================================== -->
            <div class="admin-recent">
                <div class="admin-recent-header">
                    <h3>📩 Последние обращения</h3>
                </div>
                
                @if(($stats['recent_tickets'] ?? collect())->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Нет новых обращений</p>
                    </div>
                @else
                    <div class="tickets-list-admin">
                        @foreach($stats['recent_tickets'] as $ticket)
                            <a href="{{ route('support.show', $ticket->id) }}" class="ticket-item-admin">
                                <div class="ticket-info">
                                    <span class="ticket-user">
                                        {{ $ticket->polzovatel->imya ?? $ticket->polzovatel->login }}
                                    </span>
                                    <span class="ticket-title">{{ $ticket->tema }}</span>
                                </div>
                                <div class="ticket-meta">
                                    <span class="ticket-status {{ $ticket->status }}">
                                        {{ $ticket->status == 'open' ? 'Открыт' : ($ticket->status == 'in_progress' ? 'В работе' : 'Закрыт') }}
                                    </span>
                                    <span class="ticket-time">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection