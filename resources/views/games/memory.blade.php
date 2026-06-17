@extends('layouts.app')

@section('title', 'Найди пару')

@section('content')
<div class="text-center mb-4">
    <h1 class="fw-bold" style="background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        <i class="fas fa-images me-2"></i>Найди пару
    </h1>
    <p class="text-muted">Найдите все пары одинаковых карточек</p>
</div>

<div class="memory-wrapper">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary rounded-pill px-4 py-2" id="moves">
                    <i class="fas fa-shoe-prints me-1"></i> Ходы: 0
                </span>
                <span class="badge bg-success rounded-pill px-4 py-2" id="matches">
                    <i class="fas fa-check-double me-1"></i> Пары: 0/8
                </span>
                <button id="new-memory" class="btn btn-primary rounded-pill">
                    <i class="fas fa-redo me-1"></i> Новая
                </button>
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
        
        document.getElementById('moves').textContent = `🔄 Ходы: 0`;
        document.getElementById('matches').textContent = `✅ Пары: 0/${emojis.length}`;
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
        if (lockBoard) return;
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
            document.getElementById('matches').textContent = `✅ Пары: ${matched.length/2}/${emojis.length}`;
            flipped = [];
            lockBoard = false;
            
            if (matched.length === cards.length) {
                setTimeout(() => {
                    alert('🎉 Поздравляем! Вы нашли все пары!');
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
    
    document.getElementById('new-memory').addEventListener('click', initGame);
    initGame();
});
</script>
@endpush