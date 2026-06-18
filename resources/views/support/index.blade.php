@extends('layouts.app')

@section('title', 'Центр поддержки')

@section('content')
<div class="support-container">
    <!-- ЗАГОЛОВОК -->
    <h1 class="page-title" style="font-size: 2.5rem; text-align: center; margin-bottom: 2rem;">
        💬 Центр поддержки
    </h1>

    <!-- СЕКЦИЯ: СОЗДАНИЕ ОБРАЩЕНИЯ -->
    <div class="create-ticket-section">
        <h2 style="font-size: 1.3rem; font-weight: 700; color: #1a202c; margin-bottom: 1.2rem;">
            <i class="fas fa-pen me-2" style="color: #667eea;"></i>
            Создать обращение
        </h2>
        
        <form method="POST" action="{{ route('support.store') }}" class="ticket-form">
            @csrf
            <div class="form-group">
                <label for="tema" class="form-label">Тема обращения</label>
                <input type="text" class="form-control" id="tema" name="tema" 
                       placeholder="Кратко опишите проблему..." required>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label for="tekst" class="form-label">Сообщение</label>
                <textarea class="form-control" id="tekst" name="tekst" rows="5" 
                          placeholder="Подробно опишите вашу проблему..." required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-paper-plane me-1"></i>Отправить
            </button>
        </form>
    </div>

    <!-- СЕКЦИЯ: МОИ ОБРАЩЕНИЯ -->
    <div class="my-tickets-section">
        <h2 style="font-size: 1.3rem; font-weight: 700; color: #1a202c; margin-bottom: 1.2rem;">
            <i class="fas fa-folder me-2" style="color: #667eea;"></i>
            Мои обращения
            <span style="font-size: 0.9rem; font-weight: 400; color: #a0aec0; margin-left: 0.5rem;">
                ({{ $myTickets->total() }})
            </span>
        </h2>
        
        @if($myTickets->isEmpty())
            <div class="no-items">
                <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: #cbd5e0;"></i>
                У вас пока нет обращений в поддержку
            </div>
        @else
            <div class="tickets-list">
                @foreach($myTickets as $ticket)
                    <div class="ticket-card {{ $ticket->status }}">
                        <div class="ticket-header">
                            <span class="ticket-title">{{ $ticket->tema }}</span>
                            <span class="ticket-status {{ $ticket->status }}">
                                {{ $ticket->status == 'open' ? 'Открыт' : ($ticket->status == 'in_progress' ? 'В работе' : 'Закрыт') }}
                            </span>
                        </div>
                        <div class="ticket-user">
                            От: {{ $ticket->polzovatel->imya ?? $ticket->polzovatel->login }}
                            <span style="color: #a0aec0;">(@{{ $ticket->polzovatel->login }})</span>
                        </div>
                        <div class="ticket-meta">
                            <span>Создано: {{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                            <span class="separator">|</span>
                            <span>Обновлено: {{ $ticket->updated_at->format('d.m.Y H:i') }}</span>
                            @if($ticket->soobsheniya->count() > 0)
                                <span class="separator">|</span>
                                <span><i class="far fa-comment me-1"></i>{{ $ticket->soobsheniya->count() }} сообщ.</span>
                            @endif
                        </div>
                        <a href="{{ route('support.show', $ticket->id) }}" class="btn btn-small">
                            <i class="fas fa-eye me-1"></i>Просмотреть переписку
                        </a>
                    </div>
                @endforeach
            </div>
            
            <div class="pagination-wrapper" style="margin-top: 1.5rem;">
                {{ $myTickets->links() }}
            </div>
        @endif
    </div>

    <!-- СЕКЦИЯ: ОТКРЫТЫЕ ОБРАЩЕНИЯ (только админ/модератор) -->
    @auth
        @if(auth()->user()->rol_id == 2 || auth()->user()->rol_id == 3)
            <div class="open-tickets-section">
                <h2 style="font-size: 1.3rem; font-weight: 700; color: #1a202c; margin-bottom: 1.2rem;">
                    <i class="fas fa-users me-2" style="color: #ed8936;"></i>
                    Открытые обращения
                    <span style="font-size: 0.9rem; font-weight: 400; color: #a0aec0; margin-left: 0.5rem;">
                        ({{ $openTickets->total() }})
                    </span>
                </h2>
                
                @if($openTickets->isEmpty())
                    <div class="no-items">
                        <i class="fas fa-check-circle" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: #48bb78;"></i>
                        Все обращения обработаны! Отличная работа! 🎉
                    </div>
                @else
                    <div class="tickets-list">
                        @foreach($openTickets as $ticket)
                            <div class="ticket-card {{ $ticket->status }}">
                                <div class="ticket-header">
                                    <span class="ticket-title">{{ $ticket->tema }}</span>
                                    <span class="ticket-status {{ $ticket->status }}">
                                        {{ $ticket->status == 'open' ? 'Открыт' : ($ticket->status == 'in_progress' ? 'В работе' : 'Закрыт') }}
                                    </span>
                                </div>
                                <div class="ticket-user">
                                    От: {{ $ticket->polzovatel->imya ?? $ticket->polzovatel->login }}
                                    <span style="color: #a0aec0;">(@{{ $ticket->polzovatel->login }})</span>
                                </div>
                                <div class="ticket-meta">
                                    <span>Создано: {{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                                    <span class="separator">|</span>
                                    <span>Обновлено: {{ $ticket->updated_at->format('d.m.Y H:i') }}</span>
                                    @if($ticket->soobsheniya->count() > 0)
                                        <span class="separator">|</span>
                                        <span><i class="far fa-comment me-1"></i>{{ $ticket->soobsheniya->count() }} сообщ.</span>
                                    @endif
                                </div>
                                <a href="{{ route('support.show', $ticket->id) }}" class="btn btn-small btn-primary">
                                    <i class="fas fa-comment-dots me-1"></i>Открыть чат
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="pagination-wrapper" style="margin-top: 1.5rem;">
                        {{ $openTickets->links() }}
                    </div>
                @endif
            </div>
        @endif
    @endauth
</div>
@endsection