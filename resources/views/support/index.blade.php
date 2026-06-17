@extends('layouts.app')

@section('title', 'Поддержка')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-headset text-primary"></i> Поддержка</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
        <i class="fas fa-plus"></i> Создать тикет
    </button>
</div>

<!-- Список тикетов -->
@if($tickets->isEmpty())
    <div class="alert alert-info">У вас пока нет обращений в поддержку.</div>
@else
    <div class="list-group">
        @foreach($tickets as $ticket)
            <a href="{{ route('support.show', $ticket->id) }}" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            {{ $ticket->tema }}
                            @if($ticket->status == 'open')
                                <span class="badge bg-danger">Открыт</span>
                            @elseif($ticket->status == 'in_progress')
                                <span class="badge bg-warning text-dark">В работе</span>
                            @else
                                <span class="badge bg-success">Закрыт</span>
                            @endif
                        </h5>
                        <small class="text-muted">Создан: {{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        @endforeach
    </div>
    
    <div class="mt-3">
        {{ $tickets->links() }}
    </div>
@endif

<!-- Модальное окно создания тикета -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Создать обращение</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('support.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tema" class="form-label">Тема обращения</label>
                        <input type="text" class="form-control" id="tema" name="tema" required>
                    </div>
                    <div class="mb-3">
                        <label for="tekst" class="form-label">Сообщение</label>
                        <textarea class="form-control" id="tekst" name="tekst" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection