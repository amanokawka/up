@extends('layouts.app')

@section('title', 'Найди пару')

@section('content')
<div class="text-center mb-4">
    <h1 class="page-title">🃏 Найди пару</h1>
    <p class="page-subtitle">Найдите все пары одинаковых карточек</p>
</div>

<div class="memory-wrapper">
    <div class="card-custom">
        <div class="text-center">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge badge-primary" id="moves">🔄 Ходы: 0</span>
                <span class="badge badge-success" id="matches">✅ Пары: 0/8</span>
                <button id="new-memory" class="btn btn-primary btn-sm">🔄 Новая</button>
            </div>
            
            <div id="memory-grid" class="memory-grid"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emojis = ['🎮', '🎯', '🎪', '🎨', '🎭', '🎵', '🎲', '🎳'];
    let cards = [];
    let flipped = [];
    let matched = [];
    let moves = 0;
    let lockBoard = false;
    let startTime = Date.now();
    let gameFinished = false;
    const totalPairs = emojis.length;
    
    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }
    
    function initGame() {
        const doubled = [...emojis, ...emojis];
        cards = shuffle(doubled);
        flipped = [];
        matched = [];
        moves = 0;
        lockBoard = false;
        gameFinished = false;
        startTime = Date.now();
        
        document.getElementById('moves').textContent = `🔄 Ходы: 0`;
        document.getElementById('matches').textContent = `✅ Пары: 0/${totalPairs}`;
        renderGrid();
    }
    
    function renderGrid() {
        const grid = document.getElementById('memory-grid');
        grid.innerHTML = '';
        
        cards.forEach((emoji, index) => {
            const card = document.createElement('div');
            card.className = 'memory-card';
            card.dataset.index = index;
            card.dataset.emoji = emoji;
            card.textContent = '❓';
            
            card.addEventListener('click', () => flipCard(card, index));
            grid.appendChild(card);
        });
    }
    
    function flipCard(card, index) {
        if (lockBoard || gameFinished) return;
        if (flipped.includes(index)) return;
        if (matched.includes(index)) return;
        
        card.textContent = cards[index];
        card.classList.add('flipped');
        flipped.push(index);
        
        if (flipped.length === 2) {
            moves++;
            document.getElementById('moves').textContent = `🔄 Ходы: ${moves}`;
            checkMatch();
        }
    }
    
    function checkMatch() {
        lockBoard = true;
        const [i1, i2] = flipped;
        const card1 = document.querySelector(`[data-index="${i1}"]`);
        const card2 = document.querySelector(`[data-index="${i2}"]`);
        
        if (cards[i1] === cards[i2]) {
            matched.push(i1, i2);
            card1.classList.add('matched');
            card2.classList.add('matched');
            document.getElementById('matches').textContent = `✅ Пары: ${matched.length/2}/${totalPairs}`;
            flipped = [];
            lockBoard = false;
            
            if (matched.length === cards.length) {
                gameFinished = true;
                const time = Date.now() - startTime;
                const score = Math.max(0, (totalPairs * 10) - moves + (totalPairs * 5));
                setTimeout(() => {
                    alert(`🎉 Поздравляем! Вы нашли все пары!\nХоды: ${moves}\nОчки: ${score}`);
                    saveResult(totalPairs, moves, time, score);
                }, 500);
            }
        } else {
            setTimeout(() => {
                card1.textContent = '❓';
                card1.classList.remove('flipped');
                card2.textContent = '❓';
                card2.classList.remove('flipped');
                flipped = [];
                lockBoard = false;
            }, 1000);
        }
    }
    
    function saveResult(pairs, moves, time, score) {
        fetch('{{ route("games.memory.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                pairs: pairs,
                moves: moves,
                time: Math.floor(time / 1000),
                score: score
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Результат сохранён!');
            }
        })
        .catch(error => console.error('❌ Ошибка сохранения:', error));
    }
    
    document.getElementById('new-memory').addEventListener('click', initGame);
    initGame();
});
</script>
@endpush
@endsection