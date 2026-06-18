<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Мини-Игры')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- ============================================ -->
    <!-- ОСНОВНОЙ КОНТЕЙНЕР                           -->
    <!-- ============================================ -->
    <div class="container">
        <!-- ============================================ -->
        <!-- НАВИГАЦИЯ (ВНУТРИ КОНТЕЙНЕРА)                -->
        <!-- ============================================ -->
        <nav class="navbar">
            <div class="nav-brand">
                <i class="fas fa-leaf me-2"></i>Мини-Игры
            </div>
            
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        Главная
                    </a>
                </li>
                <li>
                    <a href="{{ route('rating.index') }}" class="{{ request()->routeIs('rating.*') ? 'active' : '' }}">
                        Рейтинг
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.index') }}" class="{{ request()->routeIs('support.*') ? 'active' : '' }}">
                        Поддержка
                    </a>
                </li>
                
                @auth
                    <li>
                        <a href="{{ route('profile.index') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            Профиль
                        </a>
                    </li>
                    
                    @if(auth()->user()->rol_id == 2 || auth()->user()->rol_id == 3)
                        <li>
                            <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                Админ
                            </a>
                        </li>
                    @endif
                    
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link-btn">Выйти</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Войти</a></li>
                    <li><a href="{{ route('register') }}">Регистрация</a></li>
                @endauth
            </ul>
        </nav>

        <!-- ============================================ -->
        <!-- ОСНОВНОЙ КОНТЕНТ                             -->
        <!-- ============================================ -->
        <main>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Ошибка!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- ============================================ -->
    <!-- ПОДКЛЮЧЕНИЕ СКРИПТОВ                         -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>