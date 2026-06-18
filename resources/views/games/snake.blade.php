@extends('layouts.app')

@section('title', 'Змейка')

@section('content')
<div class="text-center mb-4">
    <h1 class="page-title">🐍 Змейка</h1>
    <p class="page-subtitle">Управляйте стрелками и собирайте еду</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card-custom snake-card">
            <div class="text-center">
                <!-- Верхняя панель -->
                <div class="snake-top-panel">
                    <span class="badge badge-primary snake-score">
                        ⭐ <span id="score">0</span>
                    </span>
                    <span class="badge badge-success snake-length">
                        📏 <span id="length">3</span>
                    </span>
                    
                    <select id="snakeColor" class="snake-color-select">
                        <option value="#11998e">🟢</option>
                        <option value="#667eea">🔵</option>
                        <option value="#f56565">🔴</option>
                        <option value="#ed8936">🟠</option>
                        <option value="#9f7aea">🟣</option>
                        <option value="#fbbf24">🟡</option>
                    </select>
                </div>
                
                <!-- Канвас -->
                <div class="snake-canvas-wrapper">
                    <canvas id="snakeCanvas" width="400" height="400"></canvas>
                </div>
                
                <!-- Кнопки -->
                <div class="snake-bottom-panel">
                    <button id="restart-snake" class="btn btn-primary btn-sm snake-btn">
                        <i class="fas fa-redo me-1"></i>Рестарт
                    </button>
                    <button id="pause-snake" class="btn btn-warning btn-sm snake-btn">
                        <i class="fas fa-pause me-1"></i>Пауза
                    </button>
                    <small class="snake-controls-hint">
                        ↑↓←→ | Пробел
                    </small>
                </div>
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
    let speed = 200;
    let startTime = Date.now();
    let gameFinished = false;
    let snakeColor = '#11998e';
    
    function hexToRgba(hex, opacity) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }
    
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#1a202c';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.strokeStyle = 'rgba(255,255,255,0.04)';
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
                ctx.fillStyle = snakeColor;
                ctx.shadowColor = snakeColor;
                ctx.shadowBlur = 15;
            } else {
                const opacity = 1 - (index / snake.length) * 0.6;
                ctx.fillStyle = hexToRgba(snakeColor, opacity);
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
    
    function initGame() {
        snake = [
            {x: 10, y: 10},
            {x: 9, y: 10},
            {x: 8, y: 10}
        ];
        direction = {x: 0, y: 0};
        score = 0;
        speed = 200;
        gameRunning = true;
        gamePaused = false;
        gameFinished = false;
        startTime = Date.now();
        snakeColor = document.getElementById('snakeColor').value;
        
        document.getElementById('score').textContent = '0';
        document.getElementById('length').textContent = '3';
        document.getElementById('pause-snake').innerHTML = '<i class="fas fa-pause me-1"></i>Пауза';
        
        generateFood();
        draw();
        
        if (gameLoop) clearInterval(gameLoop);
        gameLoop = setInterval(gameStep, speed);
    }
    
    function gameStep() {
        if (!gameRunning || gamePaused || gameFinished) return;
        if (direction.x === 0 && direction.y === 0) return;
        
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
            speed = Math.max(60, speed - 3);
            document.getElementById('score').textContent = score;
            document.getElementById('length').textContent = snake.length;
            
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
    
    function gameOver() {
        gameRunning = false;
        gameFinished = true;
        if (gameLoop) {
            clearInterval(gameLoop);
            gameLoop = null;
        }
        
        const time = Math.floor((Date.now() - startTime) / 1000);
        
        fetch('{{ route("games.snake.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                length: snake.length,
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
        ctx.fillStyle = 'rgba(255,255,255,0.6)';
        ctx.font = '16px Segoe UI, sans-serif';
        ctx.fillText('Нажмите "Рестарт"', canvas.width / 2, canvas.height / 2 + 80);
    }
    
    function togglePause() {
        if (!gameRunning || gameFinished) return;
        gamePaused = !gamePaused;
        const btn = document.getElementById('pause-snake');
        btn.innerHTML = gamePaused 
            ? '<i class="fas fa-play me-1"></i>Продолжить' 
            : '<i class="fas fa-pause me-1"></i>Пауза';
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
        if (key === ' ') { e.preventDefault(); togglePause(); }
    });
    
    document.getElementById('pause-snake').addEventListener('click', togglePause);
    document.getElementById('restart-snake').addEventListener('click', function() {
        if (gameLoop) clearInterval(gameLoop);
        initGame();
    });
    
    document.getElementById('snakeColor').addEventListener('change', function() {
        snakeColor = this.value;
        if (gameRunning && !gameFinished) draw();
    });
    
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
    
    function showStartScreen() {
        draw();
        ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = 'white';
        ctx.font = 'bold 36px Segoe UI, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('🐍 Змейка', canvas.width / 2, canvas.height / 2 - 40);
        ctx.font = '20px Segoe UI, sans-serif';
        ctx.fillStyle = 'rgba(255,255,255,0.8)';
        ctx.fillText('Нажмите любую стрелку для старта', canvas.width / 2, canvas.height / 2 + 30);
        ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
        ctx.font = '14px Segoe UI, sans-serif';
        ctx.fillText('Пробел - пауза', canvas.width / 2, canvas.height / 2 + 75);
    }
    
    snake = [
        {x: 10, y: 10},
        {x: 9, y: 10},
        {x: 8, y: 10}
    ];
    direction = {x: 0, y: 0};
    gameRunning = false;
    gameFinished = false;
    generateFood();
    showStartScreen();
    
    document.addEventListener('keydown', function firstStart(e) {
        if (gameRunning) return;
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
            document.removeEventListener('keydown', firstStart);
            initGame();
            if (e.key === 'ArrowUp') direction = {x: 0, y: -1};
            else if (e.key === 'ArrowDown') direction = {x: 0, y: 1};
            else if (e.key === 'ArrowLeft') direction = {x: -1, y: 0};
            else if (e.key === 'ArrowRight') direction = {x: 1, y: 0};
        }
    });
});
</script>
@endpush
@endsection