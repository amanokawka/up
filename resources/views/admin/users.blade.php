@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users-cog text-primary"></i> Управление пользователями</h1>
</div>

<!-- Поиск -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.search') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Поиск по имени, логину или роли..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Поиск
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Таблица пользователей -->
@if($users->isEmpty())
    <div class="alert alert-info">Пользователи не найдены.</div>
@else
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Роль</th>
                    <th>Статистика</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="Avatar" 
                                         style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                                @else
                                    <i class="fas fa-user-circle fa-2x me-2"></i>
                                @endif
                                <div>
                                    <div>{{ $user->imya ?? $user->login }}</div>
                                    <small class="text-muted">@ {{ $user->login }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <select name="rol_id" class="form-select form-select-sm" 
                                        style="width: auto; display: inline-block;"
                                        onchange="this.form.submit()">
                                    <option value="1" {{ $user->rol_id == 1 ? 'selected' : '' }}>Пользователь</option>
                                    <option value="2" {{ $user->rol_id == 2 ? 'selected' : '' }}>Модератор</option>
                                    <option value="3" {{ $user->rol_id == 3 ? 'selected' : '' }}>Администратор</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                    data-bs-target="#statsModal{{ $user->id }}">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                        </td>
                        <td>
                            @if($user->bani->where('aktiven', true)->first())
                                <span class="badge bg-danger">Заблокирован</span>
                            @else
                                <span class="badge bg-success">Активен</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($user->bani->where('aktiven', true)->first())
                                    <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#banModal{{ $user->id }}">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                @endif
                                
                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" 
                                        data-bs-target="#resetStatsModal{{ $user->id }}">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                
                                @if(Auth::user()->rol_id == 3) {{-- Только админ может удалять --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Удалить пользователя {{ $user->login }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endif

<!-- Модалки для действий -->
@foreach($users as $user)
    <!-- Модалка бана -->
    <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Блокировка пользователя {{ $user->login }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="prichina" class="form-label">Причина блокировки</label>
                            <input type="text" class="form-control" id="prichina" name="prichina" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-danger">Заблокировать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модалка сброса статистики -->
    <div class="modal fade" id="resetStatsModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Сброс статистики {{ $user->login }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.users.reset-stats', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Какую статистику сбросить?</p>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="games[]" value="sudoku" id="sudoku{{ $user->id }}">
                            <label class="form-check-label" for="sudoku{{ $user->id }}">Судоку</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="games[]" value="memory" id="memory{{ $user->id }}">
                            <label class="form-check-label" for="memory{{ $user->id }}">Найди пару</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="games[]" value="snake" id="snake{{ $user->id }}">
                            <label class="form-check-label" for="snake{{ $user->id }}">Змейка</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="games[]" value="all" id="all{{ $user->id }}">
                            <label class="form-check-label" for="all{{ $user->id }}"><strong>Всё</strong></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-warning">Сбросить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection