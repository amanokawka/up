@extends('layouts.app')

@section('title', 'Змейка')

@section('content')
<div class="text-center mb-4">
    <h1 class="page-title">🐍 Змейка</h1>
    <p class="page-subtitle">Управляйте стрелками и собирайте еду</p>
</div>

<div class="snake-wrapper">
    <div class="card-custom">
        <div class="text-center">
            <div class="mb-3">
                <span class="badge badge-primary" id="score">⭐ Очки: 0</span>
                <span class="badge badge-success" id="length">📏 Длина: 3</span>
                <button id="new-snake" class="btn btn-primary btn-sm">🔄 Новая</button>
            </div>
            
            <canvas id="snakeCanvas" width="400" height="400"></canvas>
            
            <div class="mt-3">
                <button id="pause-snake" class="btn btn-warning">⏸ Пауза</button>
                <button id="restart-snake" class="btn btn-danger">🔄 Рестарт</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('snakeCanvas');
    const ctx = canvas.getContext('2d');
    
    const gridSize = 20;
    const tileCount = canvas.width / gridSize;
    
    let snake = [];
    let direction = {x: 0, y: 0};
    let food = {};
    let score = 0;
    let gameRunning = false;
    let gamePaused = false;
    let gameLoop = null;
    let speed = 150;
    let startTime = Date.now();
    let gameFinished = false;
    
    function initGame() {
        snake = [
            {x: 10, y: 10},
            {x: 9, y: 10},
            {x: 8, y: 10}
        ];
        direction = {x: 1, y: 0};
        score = 0;
        speed = 150;
        gameRunning = true;
        gamePaused = false;
        gameFinished = false;
        startTime = Date.now();
        
        document.getElementById('score').textContent = `⭐ Очки: 0`;
        document.getElementById('length').textContent = `📏 Длина: 3`;
        document.getElementById('pause-snake').innerHTML = '⏸ Пауза';
        
        generateFood();
        draw();
        
        if (gameLoop) clearInterval(gameLoop);
        gameLoop = setInterval(gameStep, speed);
    }
    
    function generateFood() {
        let newFood;
        let isOnSnake;
        do {
            newFood = {
                x: Math.floor(Math.random() * tileCount),
                y: Math.floor(Math.random() * tileCount)
            };
            isOnSnake = snake.some(segment => segment.x === newFood.x && segment.y === newFood.y);
        } while (isOnSnake);
        food = newFood;
    }
    
    function gameStep() {
        if (!gameRunning || gamePaused || gameFinished) return;
        
        const head = {
            x: snake[0].x + direction.x,
            y: snake[0].y + direction.y
        };
        
        if (head.x < 0 || head.x >= tileCount || head.y < 0 || head.y >= tileCount) {
            gameOver();
            return;
        }
        
        if (snake.some(segment => segment.x === head.x && segment.y === head.y)) {
            gameOver();
            return;
        }
        
        snake.unshift(head);
        
        if (head.x === food.x && head.y === food.y) {
            score += 10;
            speed = Math.max(50, speed - 2);
            document.getElementById('score').textContent = `⭐ Очки: ${score}`;
            document.getElementById('length').textContent = `📏 Длина: ${snake.length}`;
            
            if (gameLoop) {
                clearInterval(gameLoop);
                gameLoop = setInterval(gameStep, speed);
            }
            generateFood();
        } else {
            snake.pop();
        }
        
        draw();
    }
    
    function draw() {
        ctx.fillStyle = '#1a202c';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.strokeStyle = 'rgba(255,255,255,0.05)';
        ctx.lineWidth = 0.5;
        for (let i = 0; i <= tileCount; i++) {
            ctx.beginPath();
            ctx.moveTo(i * gridSize, 0);
            ctx.lineTo(i * gridSize, canvas.height);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(0, i * gridSize);
            ctx.lineTo(canvas.width, i * gridSize);
            ctx.stroke();
        }
        
        snake.forEach((segment, index) => {
            const x = segment.x * gridSize;
            const y = segment.y * gridSize;
            const padding = index === 0 ? 1 : 2;
            
            if (index === 0) {
                ctx.fillStyle = '#11998e';
                ctx.shadowColor = '#11998e';
                ctx.shadowBlur = 15;
            } else {
                const progress = 1 - (index / snake.length);
                const r = Math.round(17 + 40 * progress);
                const g = Math.round(153 + 80 * progress);
                const b = Math.round(142 + 40 * progress);
                ctx.fillStyle = `rgb(${r}, ${g}, ${b})`;
                ctx.shadowBlur = 0;
            }
            
            ctx.beginPath();
            ctx.roundRect(x + padding, y + padding, gridSize - padding * 2, gridSize - padding * 2, 5);
            ctx.fill();
        });
        
        ctx.shadowBlur = 0;
        
        const fx = food.x * gridSize + gridSize / 2;
        const fy = food.y * gridSize + gridSize / 2;
        ctx.shadowColor = '#ed8936';
        ctx.shadowBlur = 20;
        ctx.fillStyle = '#ed8936';
        ctx.beginPath();
        ctx.arc(fx, fy, gridSize / 2 - 2, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.shadowBlur = 0;
        ctx.fillStyle = 'rgba(255,255,255,0.3)';
        ctx.beginPath();
        ctx.arc(fx - 3, fy - 3, 4, 0, Math.PI * 2);
        ctx.fill();
    }
    
    function saveResult(length, score, time) {
        fetch('{{ route("games.snake.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                length: length,
                score: score,
                time: time
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
    
    function gameOver() {
        gameRunning = false;
        gameFinished = true;
        if (gameLoop) {
            clearInterval(gameLoop);
            gameLoop = null;
        }
        
        const time = Math.floor((Date.now() - startTime) / 1000);
        saveResult(snake.length, score, time);
        
        draw();
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = 'white';
        ctx.font = 'bold 40px Segoe UI, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Игра окончена!', canvas.width / 2, canvas.height / 2 - 30);
        ctx.font = '24px Segoe UI, sans-serif';
        ctx.fillStyle = '#ed8936';
        ctx.fillText(`Очки: ${score} | Длина: ${snake.length}`, canvas.width / 2, canvas.height / 2 + 30);
        ctx.fillStyle = 'rgba(255,255,255,0.7)';
        ctx.font = '18px Segoe UI, sans-serif';
        ctx.fillText('Нажмите "Рестарт"', canvas.width / 2, canvas.height / 2 + 80);
    }
    
    document.addEventListener('keydown', function(e) {
        if (!gameRunning || gameFinished) return;
        const key = e.key;
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(key)) {
            e.preventDefault();
        }
        if (key === 'ArrowUp' && direction.y !== 1) direction = {x: 0, y: -1};
        else if (key === 'ArrowDown' && direction.y !== -1) direction = {x: 0, y: 1};
        else if (key === 'ArrowLeft' && direction.x !== 1) direction = {x: -1, y: 0};
        else if (key === 'ArrowRight' && direction.x !== -1) direction = {x: 1, y: 0};
    });
    
    document.getElementById('new-snake').addEventListener('click', initGame);
    document.getElementById('restart-snake').addEventListener('click', initGame);
    
    document.getElementById('pause-snake').addEventListener('click', function() {
        if (!gameRunning || gameFinished) return;
        gamePaused = !gamePaused;
        this.innerHTML = gamePaused ? '▶ Продолжить' : '⏸ Пауза';
    });
    
    // Добавляем roundRect если нет
    if (!CanvasRenderingContext2D.prototype.roundRect) {
        CanvasRenderingContext2D.prototype.roundRect = function(x, y, w, h, r) {
            if (w < 2 * r) r = w / 2;
            if (h < 2 * r) r = h / 2;
            this.moveTo(x + r, y);
            this.lineTo(x + w - r, y);
            this.quadraticCurveTo(x + w, y, x + w, y + r);
            this.lineTo(x + w, y + h - r);
            this.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
            this.lineTo(x + r, y + h);
            this.quadraticCurveTo(x, y + h, x, y + h - r);
            this.lineTo(x, y + r);
            this.quadraticCurveTo(x, y, x + r, y);
            return this;
        };
    }
    
    initGame();
});
</script>
@endpush
@endsection