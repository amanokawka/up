@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="profile-container">
            <!-- ========================================== -->
            <!-- ШАПКА ПРОФИЛЯ                              -->
            <!-- ========================================== -->
            <div class="profile-header">
                <!-- Аватар -->
                <div class="avatar-large">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->login }}">
                    @else
                        <div class="avatar-placeholder">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Информация о пользователе -->
                <div class="profile-info">
                    <h1>{{ $user->imya ?? $user->login }}</h1>
                    <div class="login-text">@ {{ $user->login }}</div>
                    
                    <div class="role-section">
                        <span class="role-badge {{ 
                            $user->rol_id == 1 ? 'user' : 
                            ($user->rol_id == 2 ? 'moderator' : 'admin') 
                        }}">
                            {{ $user->rol_id == 1 ? 'Пользователь' : ($user->rol_id == 2 ? 'Модератор' : 'Администратор') }}
                        </span>
                    </div>
                    
                    <div class="profile-actions">
                        <button class="btn btn-secondary" id="editProfileBtn">
                            <i class="fas fa-pen me-1"></i>Редактировать профиль
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- СТАТИСТИКА                                 -->
            <!-- ========================================== -->
            <div class="stats-grid">
                
                <div class="stat-card">
                    <h3>🧩 Судоку</h3>
                    @if($user->sudokuRezultati->count() > 0)
                        <div class="stat-value">{{ $user->sudokuRezultati->sum('ochki') }}</div>
                        <div class="stat-label">Всего очков</div>
                        <div class="stat-detail">
                            Игр: {{ $user->sudokuRezultati->count() }}
                            <br>
                            Лучший счёт: {{ $user->sudokuRezultati->max('ochki') ?? 0 }}
                        </div>
                    @else
                        <div class="no-stats">Нет результатов</div>
                    @endif
                </div>
                
                <div class="stat-card">
                    <h3>🃏 Найди пару</h3>
                    @if($user->naidiParuRezultati->count() > 0)
                        <div class="stat-value">{{ $user->naidiParuRezultati->sum('ochki') }}</div>
                        <div class="stat-label">Всего очков</div>
                        <div class="stat-detail">
                            Игр: {{ $user->naidiParuRezultati->count() }}
                            <br>
                            Лучший счёт: {{ $user->naidiParuRezultati->max('ochki') ?? 0 }}
                        </div>
                    @else
                        <div class="no-stats">Нет результатов</div>
                    @endif
                </div>
                
                <div class="stat-card">
                    <h3>🐍 Змейка</h3>
                    @if($user->zmeykaRezultati->count() > 0)
                        <div class="stat-value">{{ $user->zmeykaRezultati->sum('ochki') }}</div>
                        <div class="stat-label">Всего очков</div>
                        <div class="stat-detail">
                            Игр: {{ $user->zmeykaRezultati->count() }}
                            <br>
                            Лучший счёт: {{ $user->zmeykaRezultati->max('ochki') ?? 0 }}
                        </div>
                    @else
                        <div class="no-stats">Нет результатов</div>
                    @endif
                </div>
                
                <!-- Общая статистика -->
                <div class="stat-card highlight">
                    <h3>📊 Всего</h3>
                    <div class="stat-value">{{ 
                        $user->sudokuRezultati->sum('ochki') + 
                        $user->naidiParuRezultati->sum('ochki') + 
                        $user->zmeykaRezultati->sum('ochki') 
                    }}</div>
                    <div class="stat-label">Общее количество очков</div>
                    <div class="stat-detail">
                        Игр сыграно: {{ 
                            $user->sudokuRezultati->count() + 
                            $user->naidiParuRezultati->count() + 
                            $user->zmeykaRezultati->count() 
                        }}
                    </div>
                </div>
            </div>
            
            <!-- ========================================== -->
            <!-- ДАТА РЕГИСТРАЦИИ                          -->
            <!-- ========================================== -->
            <div class="profile-footer">
                <small class="text-muted">
                    <i class="far fa-calendar-alt me-1"></i>
                    Зарегистрирован: {{ $user->created_at->format('d.m.Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ              -->
<!-- ========================================== -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
        <h2>Редактировать профиль</h2>
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="edit-username">Имя пользователя</label>
                <input type="text" id="edit-username" name="imya" value="{{ old('imya', $user->imya) }}" placeholder="Введите ваше имя">
            </div>
            
            <div class="form-group">
                <label for="edit-login">Логин</label>
                <input type="text" id="edit-login" name="login" value="{{ old('login', $user->login) }}" required>
            </div>
            
            <div class="form-group">
                <label for="edit-avatar">Аватар</label>
                <input type="file" id="edit-avatar" name="avatar" accept="image/*">
                <small class="text-muted">PNG, JPG, до 2MB</small>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Сохранить изменения
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editModal');
    const openBtn = document.getElementById('editProfileBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    openBtn.addEventListener('click', function() {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});
</script>
@endpush