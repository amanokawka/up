@extends('layouts.app')

@section('title', 'Змейка')

@section('content')
<div class="text-center mb-4">
    <h1 style="color: white; font-weight: 700; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
        <i class="fas fa-dragon me-2"></i>Змейка
    </h1>
    <p style="color: rgba(255,255,255,0.9);">Управляйте стрелками и собирайте еду</p>
</div>

<div class="snake-wrapper">
    <div class="card-custom">
        <div class="text-center">
            <div class="mb-3">
                <span class="badge badge-primary" id="score">
                    <i class="fas fa-star me-1"></i> Очки: 0
                </span>
                <span class="badge badge-success" id="length">
                    <i class="fas fa-ruler me-1"></i> Длина: 1
                </span>
                <button id="new-snake" class="btn btn-primary btn-sm">
                    <i class="fas fa-redo me-1"></i> Новая
                </button>
            </div>
            
            <canvas id="snakeCanvas" width="400" height="400"></canvas>
            
            <div class="mt-3">
                <button id="pause-snake" class="btn btn-warning">
                    <i class="fas fa-pause me-1"></i> Пауза
                </button>
                <button id="restart-snake" class="btn btn-danger">
                    <i class="fas fa-redo me-1"></i> Рестарт
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Змейка загружена!');
    
    const canvas = document.getElementById('snakeCanvas');
    const ctx = canvas.getContext('2d');
    
    // Размеры
    const gridSize = 20;
    const tileCount = canvas.width / gridSize;
    
    // Состояние игры
    let snake = [];
    let direction = {x: 0, y: 0};
    let food = {};
    let score = 0;
    let gameRunning = false;
    let gamePaused = false;
    let gameLoop = null;
    let speed = 150;
    
    // Инициализация игры
    function initGame() {
        console.log('Инициализация игры...');
        
        // Начальная позиция змейки (центр)
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
        
        document.getElementById('score').textContent = '⭐ Очки: 0';
        document.getElementById('length').textContent = '📏 Длина: 3';
        document.getElementById('pause-snake').innerHTML = '<i class="fas fa-pause me-1"></i> Пауза';
        
        generateFood();
        draw();
        
        // Очищаем старый интервал
        if (gameLoop) {
            clearInterval(gameLoop);
        }
        
        // Запускаем новый
        gameLoop = setInterval(gameStep, speed);
        console.log('Игра запущена!');
    }
    
    // Генерация еды
    function generateFood() {
        let newFood;
        let isOnSnake;
        
        do {
            newFood = {
                x: Math.floor(Math.random() * tileCount),
                y: Math.floor(Math.random() * tileCount)
            };
            
            isOnSnake = snake.some(segment => 
                segment.x === newFood.x && segment.y === newFood.y
            );
        } while (isOnSnake);
        
        food = newFood;
        console.log('Еда создана:', food);
    }
    
    // Шаг игры
    function gameStep() {
        if (!gameRunning || gamePaused) return;
        
        // Двигаем змейку
        const head = {
            x: snake[0].x + direction.x,
            y: snake[0].y + direction.y
        };
        
        // Проверка столкновения со стенами
        if (head.x < 0 || head.x >= tileCount || head.y < 0 || head.y >= tileCount) {
            gameOver();
            return;
        }
        
        // Проверка столкновения с собой
        if (snake.some(segment => segment.x === head.x && segment.y === head.y)) {
            gameOver();
            return;
        }
        
        // Добавляем новую голову
        snake.unshift(head);
        
        // Проверка съедания еды
        if (head.x === food.x && head.y === food.y) {
            score += 10;
            speed = Math.max(50, speed - 2);
            
            document.getElementById('score').textContent = `⭐ Очки: ${score}`;
            document.getElementById('length').textContent = `📏 Длина: ${snake.length}`;
            
            // Обновляем скорость
            if (gameLoop) {
                clearInterval(gameLoop);
                gameLoop = setInterval(gameStep, speed);
            }
            
            generateFood();
        } else {
            // Удаляем хвост
            snake.pop();
        }
        
        draw();
    }
    
    // Отрисовка
    function draw() {
        // Очищаем холст
        ctx.fillStyle = '#1a202c';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Рисуем сетку (для красоты)
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
        
        // Рисуем змейку
        snake.forEach((segment, index) => {
            const x = segment.x * gridSize;
            const y = segment.y * gridSize;
            const padding = index === 0 ? 1 : 2;
            
            // Голова - яркая, тело - градиент
            if (index === 0) {
                ctx.fillStyle = '#667eea';
                ctx.shadowColor = '#667eea';
                ctx.shadowBlur = 15;
            } else {
                const progress = 1 - (index / snake.length);
                const r = Math.round(102 + 30 * progress);
                const g = Math.round(126 + 80 * progress);
                const b = Math.round(234 + 20 * progress);
                ctx.fillStyle = `rgb(${r}, ${g}, ${b})`;
                ctx.shadowBlur = 0;
            }
            
            ctx.beginPath();
            ctx.roundRect(x + padding, y + padding, gridSize - padding * 2, gridSize - padding * 2, 5);
            ctx.fill();
        });
        
        ctx.shadowBlur = 0;
        
        // Рисуем еду
        const fx = food.x * gridSize + gridSize / 2;
        const fy = food.y * gridSize + gridSize / 2;
        const radius = gridSize / 2 - 2;
        
        ctx.shadowColor = '#ed8936';
        ctx.shadowBlur = 20;
        ctx.fillStyle = '#ed8936';
        ctx.beginPath();
        ctx.arc(fx, fy, radius, 0, Math.PI * 2);
        ctx.fill();
        
        // Блик на еде
        ctx.shadowBlur = 0;
        ctx.fillStyle = 'rgba(255,255,255,0.3)';
        ctx.beginPath();
        ctx.arc(fx - 3, fy - 3, 4, 0, Math.PI * 2);
        ctx.fill();
    }
    
    // Game Over
    function gameOver() {
        gameRunning = false;
        if (gameLoop) {
            clearInterval(gameLoop);
            gameLoop = null;
        }
        
        draw();
        
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = 'white';
        ctx.font = 'bold 40px Segoe UI, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Игра окончена!', canvas.width / 2, canvas.height / 2 - 30);
        
        ctx.font = '24px Segoe UI, sans-serif';
        ctx.fillStyle = '#ed8936';
        ctx.fillText(`Очки: ${score}`, canvas.width / 2, canvas.height / 2 + 30);
        
        ctx.fillStyle = 'rgba(255,255,255,0.7)';
        ctx.font = '18px Segoe UI, sans-serif';
        ctx.fillText('Нажмите "Рестарт"', canvas.width / 2, canvas.height / 2 + 80);
    }
    
    // Управление
    document.addEventListener('keydown', function(e) {
        if (!gameRunning) return;
        
        const key = e.key;
        
        // Блокируем прокрутку страницы
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(key)) {
            e.preventDefault();
        }
        
        // Меняем направление
        if (key === 'ArrowUp' && direction.y !== 1) {
            direction = {x: 0, y: -1};
        } else if (key === 'ArrowDown' && direction.y !== -1) {
            direction = {x: 0, y: 1};
        } else if (key === 'ArrowLeft' && direction.x !== 1) {
            direction = {x: -1, y: 0};
        } else if (key === 'ArrowRight' && direction.x !== -1) {
            direction = {x: 1, y: 0};
        }
    });
    
    // Кнопки
    document.getElementById('new-snake').addEventListener('click', function() {
        initGame();
    });
    
    document.getElementById('restart-snake').addEventListener('click', function() {
        initGame();
    });
    
    document.getElementById('pause-snake').addEventListener('click', function() {
        if (!gameRunning) return;
        
        gamePaused = !gamePaused;
        this.innerHTML = gamePaused 
            ? '<i class="fas fa-play me-1"></i> Продолжить' 
            : '<i class="fas fa-pause me-1"></i> Пауза';
    });
    
    // Добавляем метод roundRect для canvas
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
    
    // Старт!
    initGame();
    console.log('Змейка готова!');
});
</script>
@endpush