@extends('layouts.app')

@section('title', 'Тикет #' . $ticket->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-0">
            <!-- ШАПКА ЧАТА -->
            <div class="chat-header">
                <div>
                    <h5 class="ticket-title">
                        <i class="fas fa-ticket-alt me-2"></i>
                        {{ $ticket->tema }}
                    </h5>
                    <div class="ticket-meta">
                        <span>Автор: {{ $ticket->polzovatel->imya ?? $ticket->polzovatel->login }}</span>
                        <span>•</span>
                        <span>{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                        @if($ticket->moderator_id)
                            <span>•</span>
                            <span>
                                Модератор: {{ $ticket->moderator->imya ?? $ticket->moderator->login }}
                                <span class="staff-badge {{ $ticket->moderator->rol_id == 3 ? 'admin' : ($ticket->moderator->rol_id == 2 ? 'moderator' : 'staff') }}">
                                    {{ $ticket->moderator->rol_id == 3 ? 'Админ' : ($ticket->moderator->rol_id == 2 ? 'Модератор' : 'Сотрудник') }}
                                </span>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="ticket-status {{ $ticket->status }}">
                        {{ $ticket->status == 'open' ? 'Открыт' : ($ticket->status == 'in_progress' ? 'В работе' : 'Закрыт') }}
                    </span>
                    
                    <!-- ⭐ КНОПКА ЗАКРЫТИЯ ТИКЕТА ⭐ -->
                    @auth
                        @if((auth()->user()->rol_id == 2 || auth()->user()->rol_id == 3) && $ticket->status != 'closed')
                            <button type="button" class="btn-close-ticket" 
                                    onclick="closeTicket({{ $ticket->id }})">
                                <i class="fas fa-check me-1"></i>Закрыть
                            </button>
                        @endif
                    @endauth
                </div>
            </div>
            
            <!-- ОБЛАСТЬ СООБЩЕНИЙ -->
            <div class="chat-messages" id="chatMessages">
                @foreach($ticket->soobsheniya as $message)
                    @php
                        $isStaff = $message->ot_personala;
                        $user = $message->polzovatel;
                    @endphp
                    
                    <div class="chat-message {{ $isStaff ? 'staff-message' : 'user-message' }}">
                        <div class="chat-avatar">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->login }}">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        
                        <div class="message-bubble">
                            <div class="message-meta">
                                <span class="message-author">
                                    {{ $isStaff ? 'Сотрудник' : ($user->imya ?? $user->login) }}
                                    @if($isStaff)
                                        <span class="staff-badge {{ $user->rol_id == 3 ? 'admin' : ($user->rol_id == 2 ? 'moderator' : 'staff') }}">
                                            {{ $user->rol_id == 3 ? 'Админ' : ($user->rol_id == 2 ? 'Модератор' : 'Сотрудник') }}
                                        </span>
                                    @endif
                                </span>
                                <span class="message-time">{{ $message->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <p>{{ $message->tekst }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- ПОЛЕ ВВОДА -->
            <div class="chat-input-area">
                @if($ticket->status != 'closed')
                    <form class="chat-form" id="chatForm" method="POST" action="{{ route('support.message', $ticket->id) }}">
                        @csrf
                        <textarea name="tekst" id="messageInput" rows="1" 
                                  placeholder="Введите сообщение..." 
                                  required autofocus></textarea>
                        <button type="submit" class="btn-send">
                            <i class="fas fa-paper-plane me-1"></i>Отправить
                        </button>
                    </form>
                @else
                    <div class="ticket-closed-notice">
                        <i class="fas fa-check-circle"></i>
                        <p>Тикет закрыт. Новые сообщения нельзя отправить.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('support.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Назад к списку
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('chatMessages');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.focus();
        
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        messageInput.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('chatForm').submit();
            }
        });
    }
});

// ⭐ ФУНКЦИЯ ЗАКРЫТИЯ ТИКЕТА ⭐
function closeTicket(ticketId) {
    if (!confirm('Вы уверены, что хотите закрыть этот тикет?')) return;
    
    fetch('/support/' + ticketId + '/close', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Ошибка при закрытии тикета');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Ошибка при закрытии тикета');
    });
}
</script>
@endpush
@endsection