@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="users-container">
            <!-- Заголовок -->
            <div class="users-header">
                <div>
                    <h1 class="users-title">👥 Управление пользователями</h1>
                    <p class="users-subtitle">Всего пользователей: <strong>{{ $users->total() }}</strong></p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ПОИСК                                      -->
            <!-- ========================================== -->
            <div class="users-search">
                <form action="{{ route('admin.users.search') }}" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="search" class="search-input" 
                               placeholder="Поиск по имени, логину или роли..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-search">
                            <i class="fas fa-search me-1"></i>Поиск
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-reset">
                                <i class="fas fa-times me-1"></i>Сбросить
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- ========================================== -->
            <!-- ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ                     -->
            <!-- ========================================== -->
            @if($users->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>Пользователи не найдены</p>
                </div>
            @else
                <div class="users-table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Пользователь</th>
                                <th style="width: 140px;">Роль</th>
                                <th style="width: 80px;">Игр</th>
                                <th style="width: 100px;">Очков</th>
                                <th style="width: 110px;">Статус</th>
                                <th style="width: 220px;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $isBanned = $user->bani->where('aktiven', true)->first();
                                    $gamesCount = $user->sudokuRezultati->count() + 
                                                  $user->naidiParuRezultati->count() + 
                                                  $user->zmeykaRezultati->count();
                                    $totalScore = $user->sudokuRezultati->sum('ochki') + 
                                                  $user->naidiParuRezultati->sum('ochki') + 
                                                  $user->zmeykaRezultati->sum('ochki');
                                    $isAdmin = $user->rol_id == 3;
                                    $isModerator = $user->rol_id == 2;
                                    $isSelf = auth()->id() == $user->id;
                                    $currentUserIsAdmin = auth()->user()->rol_id == 3;
                                    $currentUserIsModerator = auth()->user()->rol_id == 2;
                                    
                                    // Права на блокировку
                                    $canBan = !$isSelf && ($currentUserIsAdmin || $user->rol_id == 1);
                                    
                                    // Права на сброс статистики
                                    $canReset = !$isSelf && ($currentUserIsAdmin || $user->rol_id == 1);
                                    
                                    // Права на удаление
                                    $canDelete = $currentUserIsAdmin && !$isSelf && $user->rol_id != 3;
                                @endphp
                                <tr class="{{ $isBanned ? 'banned' : '' }} {{ $isAdmin ? 'admin-row' : '' }} {{ $isModerator ? 'moderator-row' : '' }}">
                                    <td class="user-id">{{ $user->id }}</td>
                                    <td>
                                        <div class="user-cell">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="{{ $user->login }}" class="user-avatar">
                                            @else
                                                <div class="user-avatar avatar-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <div class="user-details">
                                                <span class="user-name">
                                                    {{ $user->imya ?? $user->login }}
                                                    @if($isSelf)
                                                        <span class="user-self-badge">Вы</span>
                                                    @endif
                                                </span>
                                                <span class="user-login">@ {{ $user->login }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($currentUserIsAdmin && !$isSelf)
                                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="role-form">
                                                @csrf
                                                <select name="rol_id" class="role-select" onchange="this.form.submit()">
                                                    <option value="1" {{ $user->rol_id == 1 ? 'selected' : '' }}>👤 Пользователь</option>
                                                    <option value="2" {{ $user->rol_id == 2 ? 'selected' : '' }}>🛡️ Модератор</option>
                                                    <option value="3" {{ $user->rol_id == 3 ? 'selected' : '' }}>⚡ Админ</option>
                                                </select>
                                            </form>
                                        @else
                                            <span class="role-badge {{ 
                                                $user->rol_id == 1 ? 'user' : 
                                                ($user->rol_id == 2 ? 'moderator' : 'admin') 
                                            }}">
                                                {{ $user->rol_id == 1 ? 'Пользователь' : ($user->rol_id == 2 ? 'Модератор' : 'Администратор') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $gamesCount }}</td>
                                    <td class="text-center">{{ $totalScore }}</td>
                                    <td>
                                        @if($isBanned)
                                            <span class="status-badge banned">🔒 Заблокирован</span>
                                        @else
                                            <span class="status-badge active">✅ Активен</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Блокировка/Разблокировка -->
                                            @if($canBan)
                                                @if($isBanned)
                                                    <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="action-form">
                                                        @csrf
                                                        <button type="submit" class="btn-small btn-success" title="Разблокировать">
                                                            <i class="fas fa-unlock"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn-small btn-warning" 
                                                            onclick="openBanModal({{ $user->id }}, '{{ $user->imya ?? $user->login }}')" 
                                                            title="Заблокировать">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            <!-- Сброс статистики -->
                                            @if($canReset)
                                                <button type="button" class="btn-small btn-secondary" 
                                                        onclick="openResetModal(
                                                            {{ $user->id }}, 
                                                            '{{ $user->imya ?? $user->login }}',
                                                            {{ $user->sudokuRezultati->sum('ochki') }},
                                                            {{ $user->naidiParuRezultati->sum('ochki') }},
                                                            {{ $user->zmeykaRezultati->sum('ochki') }}
                                                        )" 
                                                        title="Сбросить статистику">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                            @endif

                                            <!-- Удалить (только админ) -->
                                            @if($canDelete)
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="action-form" 
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить пользователя {{ $user->login }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-small btn-danger" title="Удалить">
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

                <!-- ========================================== -->
                <!-- ПАГИНАЦИЯ                                 -->
                <!-- ========================================== -->
                <div class="users-pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- МОДАЛЬНОЕ ОКНО БЛОКИРОВКИ                  -->
<!-- ========================================== -->
<div id="banModal" class="modal">
    <div class="modal-content" style="max-width: 450px;">
        <span class="close" id="closeBanModal">&times;</span>
        <h2>🔒 Заблокировать пользователя</h2>
        <p style="color: #4a5568; margin-bottom: 1.5rem;">
            Пользователь: <strong id="banUsername"></strong>
        </p>
        <form id="banForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="prichina" class="form-label">Причина блокировки</label>
                <input type="text" class="form-control" id="prichina" name="prichina" 
                       placeholder="Нарушение правил..." required>
            </div>
            <button type="submit" class="btn btn-danger" style="width: 100%;">
                <i class="fas fa-lock me-1"></i>Заблокировать
            </button>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- МОДАЛЬНОЕ ОКНО СБРОСА СТАТИСТИКИ           -->
<!-- ========================================== -->
<div id="resetModal" class="modal">
    <div class="modal-content" style="max-width: 480px;">
        <span class="close" id="closeResetModal">&times;</span>
        <h2>🔄 Сброс статистики</h2>
        <p style="color: #4a5568; margin-bottom: 1rem;">
            Пользователь: <strong id="resetUsername"></strong>
        </p>
        <form id="resetForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Выберите игру для сброса:</label>
                <div class="reset-options">
                    <label class="reset-option">
                        <input type="checkbox" name="games[]" value="sudoku" class="game-checkbox">
                        <span class="reset-option-icon">🧩</span>
                        <span class="reset-option-label">Судоку</span>
                        <span class="reset-option-count" id="sudokuPoints">0 очков</span>
                    </label>
                    <label class="reset-option">
                        <input type="checkbox" name="games[]" value="memory" class="game-checkbox">
                        <span class="reset-option-icon">🃏</span>
                        <span class="reset-option-label">Найди пару</span>
                        <span class="reset-option-count" id="memoryPoints">0 очков</span>
                    </label>
                    <label class="reset-option">
                        <input type="checkbox" name="games[]" value="snake" class="game-checkbox">
                        <span class="reset-option-icon">🐍</span>
                        <span class="reset-option-label">Змейка</span>
                        <span class="reset-option-count" id="snakePoints">0 очков</span>
                    </label>
                    <label class="reset-option all-option">
                        <input type="checkbox" id="selectAllCheckbox" onclick="toggleAllCheckboxes(this)">
                        <span class="reset-option-icon">📊</span>
                        <span class="reset-option-label"><strong>Выбрать всё</strong></span>
                        <span class="reset-option-count" id="totalPoints">0 очков</span>
                    </label>
                </div>
                <small class="text-muted" style="display: block; margin-top: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Будут удалены все записи по выбранным играм
                </small>
            </div>
            <button type="submit" class="btn btn-warning" style="width: 100%;" id="resetSubmitBtn" disabled>
                <i class="fas fa-undo-alt me-1"></i>Сбросить выбранное
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ==========================================
// МОДАЛЬНОЕ ОКНО БЛОКИРОВКИ
// ==========================================
function openBanModal(userId, username) {
    const modal = document.getElementById('banModal');
    document.getElementById('banUsername').textContent = username;
    document.getElementById('banForm').action = '/admin/users/' + userId + '/ban';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

document.getElementById('closeBanModal').addEventListener('click', function() {
    const modal = document.getElementById('banModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
});

// ==========================================
// МОДАЛЬНОЕ ОКНО СБРОСА СТАТИСТИКИ
// ==========================================
function openResetModal(userId, username, sudokuPoints, memoryPoints, snakePoints) {
    const modal = document.getElementById('resetModal');
    document.getElementById('resetUsername').textContent = username;
    document.getElementById('resetForm').action = '/admin/users/' + userId + '/reset-stats';
    
    // Подставляем очки
    document.getElementById('sudokuPoints').textContent = sudokuPoints + ' очков';
    document.getElementById('memoryPoints').textContent = memoryPoints + ' очков';
    document.getElementById('snakePoints').textContent = snakePoints + ' очков';
    
    const total = sudokuPoints + memoryPoints + snakePoints;
    document.getElementById('totalPoints').textContent = total + ' очков';
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Сбрасываем все чекбоксы
    document.querySelectorAll('#resetModal .game-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    document.getElementById('resetSubmitBtn').disabled = true;
}

document.getElementById('closeResetModal').addEventListener('click', function() {
    const modal = document.getElementById('resetModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
});

// ==========================================
// ВКЛЮЧЕНИЕ/ВЫКЛЮЧЕНИЕ КНОПКИ "СБРОСИТЬ"
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('#resetModal .game-checkbox');
    const submitBtn = document.getElementById('resetSubmitBtn');
    const selectAll = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const anyChecked = document.querySelectorAll('#resetModal .game-checkbox:checked').length > 0;
            submitBtn.disabled = !anyChecked;
            
            const allChecked = document.querySelectorAll('#resetModal .game-checkbox:checked').length === checkboxes.length;
            if (allChecked && checkboxes.length > 0) {
                selectAll.checked = true;
            } else {
                selectAll.checked = false;
            }
        });
    });
});

function toggleAllCheckboxes(allCheckbox) {
    const checkboxes = document.querySelectorAll('#resetModal .game-checkbox');
    const submitBtn = document.getElementById('resetSubmitBtn');
    checkboxes.forEach(cb => cb.checked = allCheckbox.checked);
    submitBtn.disabled = !allCheckbox.checked;
}

// ==========================================
// ЗАКРЫТИЕ ПО КЛИКУ НА ФОН
// ==========================================
window.addEventListener('click', function(event) {
    const banModal = document.getElementById('banModal');
    const resetModal = document.getElementById('resetModal');
    if (event.target === banModal) {
        banModal.style.display = 'none';
        document.body.style.overflow = '';
    }
    if (event.target === resetModal) {
        resetModal.style.display = 'none';
        document.body.style.overflow = '';
    }
});

// ==========================================
// ЗАКРЫТИЕ ПО ESCAPE
// ==========================================
window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById('banModal').style.display = 'none';
        document.getElementById('resetModal').style.display = 'none';
        document.body.style.overflow = '';
    }
});
</script>
@endpush