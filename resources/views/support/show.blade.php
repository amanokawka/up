@extends('layouts.app')

@section('title', 'Тикет #' . $ticket->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-ticket-alt text-primary"></i> 
        Тикет #{{ $ticket->id }}: {{ $ticket->tema }}
    </h1>
    <a href="{{ route('support.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Назад
    </a>
</div>

<!-- Статус тикета -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Статус:</strong>
                @if($ticket->status == 'open')
                    <span class="badge bg-danger">Открыт</span>
                @elseif($ticket->status == 'in_progress')
                    <span class="badge bg-warning text-dark">В работе</span>
                @else
                    <span class="badge bg-success">Закрыт</span>
                @endif
            </div>
            <div class="col-md-6 text-md-end">
                <strong>Создан:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}
                <br>
                <strong>Последнее обновление:</strong> {{ $ticket->updated_at->format('d.m.Y H:i') }}
            </div>
        </div>
        @if($ticket->moderator_id)
            <div class="mt-2">
                <strong>Модератор:</strong> {{ $ticket->moderator->imya ?? $ticket->moderator->login ?? 'Не назначен' }}
            </div>
        @endif
    </div>
</div>

<!-- Сообщения -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-comments"></i> Сообщения</h5>
    </div>
    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
        @foreach($ticket->soobsheniya as $message)
            <div class="mb-3 {{ $message->ot_personala ? 'text-end' : '' }}">
                <div class="d-inline-block p-3 rounded {{ $message->ot_personala ? 'bg-primary text-white' : 'bg-light' }}" 
                     style="max-width: 80%;">
                    <div class="mb-1">
                        <strong>{{ $message->ot_personala ? 'Модератор' : ($message->polzovatel->imya ?? $message->polzovatel->login) }}</strong>
                        <small class="text-muted ms-2">{{ $message->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <p class="mb-0">{{ $message->tekst }}</p>
                </div>
            </div>
        @endforeach
    </div>
    <div class="card-footer">
        <form action="{{ route('support.message', $ticket->id) }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control" name="tekst" placeholder="Введите сообщение..." required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Отправить
                </button>
            </div>
        </form>
    </div>
</div>
@endsection