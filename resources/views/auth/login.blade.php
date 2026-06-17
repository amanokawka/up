@extends('layouts.app')

@section('title', 'Вход')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card-custom">
            <div class="text-center mb-4">
                <i class="fas fa-sign-in-alt" style="font-size: 3rem; color: #11998e;"></i>
                <h2 class="mt-2" style="font-weight: 700; color: #1a202c;">Вход</h2>
                <p style="color: #4a5568;">Войдите в свой аккаунт</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="login" class="form-label">Логин</label>
                    <input type="text" class="form-control @error('login') is-invalid @enderror" 
                           id="login" name="login" value="{{ old('login') }}" required autofocus>
                    @error('login')
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
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Запомнить меня</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Войти
                </button>
            </form>
            
            <div class="text-center mt-3">
                <p style="color: #4a5568;">
                    Нет аккаунта? 
                    <a href="{{ route('register') }}" style="color: #11998e; text-decoration: none; font-weight: 600;">
                        Зарегистрироваться
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection