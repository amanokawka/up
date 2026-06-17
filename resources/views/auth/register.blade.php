@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-custom">
            <div class="text-center mb-4">
                <i class="fas fa-user-plus" style="font-size: 3rem; color: #11998e;"></i>
                <h2 class="mt-2" style="font-weight: 700; color: #1a202c;">Регистрация</h2>
                <p style="color: #4a5568;">Создайте новый аккаунт</p>
            </div>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="login" class="form-label">Логин</label>
                    <input type="text" class="form-control @error('login') is-invalid @enderror" 
                           id="login" name="login" value="{{ old('login') }}" required>
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="imya" class="form-label">Имя (необязательно)</label>
                    <input type="text" class="form-control @error('imya') is-invalid @enderror" 
                           id="imya" name="imya" value="{{ old('imya') }}">
                    @error('imya')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                </button>
            </form>
            
            <div class="text-center mt-3">
                <p style="color: #4a5568;">
                    Уже есть аккаунт? 
                    <a href="{{ route('login') }}" style="color: #11998e; text-decoration: none; font-weight: 600;">
                        Войти
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection