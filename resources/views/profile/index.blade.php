@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
<div class="row">
    <!-- Аватар и основная информация -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Avatar" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <i class="fas fa-user-circle fa-8x text-secondary mb-3"></i>
                @endif
                
                <h4>{{ $user->imya ?? $user->login }}</h4>
                <p class="text-muted">@ {{ $user->login }}</p>
                
                <span class="badge bg-info">
                    @if($user->rol_id == 1) Пользователь
                    @elseif($user->rol_id == 2) Модератор
                    @else Администратор
                    @endif
                </span>
                
                <div class="mt-3">
                    <small class="text-muted">Дата регистрации: {{ $user->created_at->format('d.m.Y') }}</small>
                </div>
            </div>
        </div>

        <!-- Статистика -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Моя статистика</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Судоку:</span>
                    <strong>{{ $user->sudokuRezultati->sum('ochki') ?? 0 }} очков</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Найди пару:</span>
                    <strong>{{ $user->naidiParuRezultati->sum('ochki') ?? 0 }} очков</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Змейка:</span>
                    <strong>{{ $user->zmeykaRezultati->sum('ochki') ?? 0 }} очков</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Всего игр:</span>
                    <strong>
                        {{ $user->sudokuRezultati->count() + 
                           $user->naidiParuRezultati->count() + 
                           $user->zmeykaRezultati->count() }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Редактирование профиля -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Редактировать профиль</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="imya" class="form-label">Имя</label>
                        <input type="text" class="form-control @error('imya') is-invalid @enderror" 
                               id="imya" name="imya" value="{{ old('imya', $user->imya) }}">
                        @error('imya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" class="form-control @error('login') is-invalid @enderror" 
                               id="login" name="login" value="{{ old('login', $user->login) }}" required>
                        @error('login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                </form>
            </div>
        </div>

        <!-- Смена аватара -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-image"></i> Сменить аватар</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Выберите изображение (PNG, JPG, до 2MB)</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                               id="avatar" name="avatar" accept="image/*">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-upload"></i> Загрузить аватар
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection