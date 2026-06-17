@extends('layouts.app')

@section('title', 'Судоку')

@section('content')
<div class="text-center mb-4">
    <h1 class="fw-bold" style="background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        <i class="fas fa-th me-2"></i>Судоку
    </h1>
    <p class="text-muted">Заполните сетку цифрами от 1 до 9</p>
</div>

<div class="sudoku-wrapper">
    <div class="text-center mb-3">
        <select id="difficulty" class="form-select" style="max-width: 200px; margin: 0 auto; border-radius: 50px;">
            <option value="easy">😊 Легкий</option>
            <option value="medium" selected>🤔 Средний</option>
            <option value="hard">😈 Сложный</option>
        </select>
    </div>
    
    <div id="sudoku-grid" class="sudoku-grid">
        <!-- Генерируется JS -->
    </div>
    
    <div class="sudoku-controls">
        <button id="check-sudoku" class="btn btn-success">
            <i class="fas fa-check me-1"></i> Проверить
        </button>
        <button id="new-sudoku" class="btn btn-primary">
            <i class="fas fa-redo me-1"></i> Новая
        </button>
        <button id="hint-sudoku" class="btn btn-info">
            <i class="fas fa-lightbulb me-1"></i> Подсказка
        </button>
    </div>
    
    <div id="sudoku-result" class="mt-3"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentBoard = [];
    let solution = [];
    let givenCells = [];
    let selectedDifficulty = 'medium';
    
    // Генерация простого судоку (для демонстрации)
    function generateSudoku(difficulty) {
        const size = 9;
        const board = Array.from({length: size}, () => Array(size).fill(0));
        
        // Заполняем диагональные блоки
        for (let block = 0; block < size; block += 3) {
            const nums = shuffle([1,2,3,4,5,6,7,8,9]);
            let idx = 0;
            for (let i = block; i < block + 3; i++) {
                for (let j = block; j < block + 3; j++) {
                    board[i][j] = nums[idx++];
                }
            }
        }
        
        // Решаем судоку
        solveSudoku(board);
        
        // Копируем решение
        solution = board.map(row => [...row]);
        
        // Удаляем ячейки в зависимости от сложности
        const cellsToRemove = difficulty === 'easy' ? 30 : difficulty === 'medium' ? 45 : 55;
        let removed = 0;
        givenCells = [];
        
        while (removed < cellsToRemove) {
            const row = Math.floor(Math.random() * size);
            const col = Math.floor(Math.random() * size);
            if (board[row][col] !== 0) {
                board[row][col] = 0;
                givenCells.push({row, col, value: solution[row][col]});
                removed++;
            }
        }
        
        return board;
    }
    
    function solveSudoku(board) {
        const size = 9;
        for (let row = 0; row < size; row++) {
            for (let col = 0; col < size; col++) {
                if (board[row][col] === 0) {
                    for (let num = 1; num <= 9; num++) {
                        if (isValid(board, row, col, num)) {
                            board[row][col] = num;
                            if (solveSudoku(board)) {
                                return true;
                            }
                            board[row][col] = 0;
                        }
                    }
                    return false;
                }
            }
        }
        return true;
    }
    
    function isValid(board, row, col, num) {
        const size = 9;
        for (let i = 0; i < size; i++) {
            if (board[row][i] === num) return false;
            if (board[i][col] === num) return false;
        }
        
        const blockRow = Math.floor(row / 3) * 3;
        const blockCol = Math.floor(col / 3) * 3;
        for (let i = blockRow; i < blockRow + 3; i++) {
            for (let j = blockCol; j < blockCol + 3; j++) {
                if (board[i][j] === num) return false;
            }
        }
        return true;
    }
    
    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }
    
    function renderGrid() {
        const difficulty = document.getElementById('difficulty').value;
        currentBoard = generateSudoku(difficulty);
        const grid = document.getElementById('sudoku-grid');
        grid.innerHTML = '';
        
        for (let i = 0; i < 9; i++) {
            for (let j = 0; j < 9; j++) {
                const input = document.createElement('input');
                input.type = 'text';
                input.maxLength = 1;
                input.dataset.row = i;
                input.dataset.col = j;
                
                if (currentBoard[i][j] !== 0) {
                    input.value = currentBoard[i][j];
                    input.classList.add('given');
                    input.readOnly = true;
                }
                
                if (j % 3 === 2 && j !== 8) input.classList.add('border-right');
                if (i % 3 === 2 && i !== 8) input.classList.add('border-bottom');
                
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^1-9]/g, '');
                    this.classList.remove('error', 'success');
                });
                
                grid.appendChild(input);
            }
        }
    }
    
    // Проверка
    document.getElementById('check-sudoku').addEventListener('click', function() {
        const inputs = document.querySelectorAll('#sudoku-grid input');
        let correct = 0;
        let total = 0;
        
        inputs.forEach(input => {
            if (!input.readOnly) {
                total++;
                const row = parseInt(input.dataset.row);
                const col = parseInt(input.dataset.col);
                const value = parseInt(input.value);
                
                if (value === solution[row][col]) {
                    correct++;
                    input.classList.remove('error');
                    input.classList.add('success');
                } else if (value >= 1 && value <= 9) {
                    input.classList.remove('success');
                    input.classList.add('error');
                }
            }
        });
        
        const result = document.getElementById('sudoku-result');
        if (total === 0) {
            result.innerHTML = '<div class="alert alert-warning rounded-pill">Заполните хотя бы одну клетку!</div>';
        } else if (correct === total && total > 0) {
            result.innerHTML = '<div class="alert alert-success rounded-pill">🎉 Отлично! Все верно!</div>';
        } else {
            result.innerHTML = `<div class="alert alert-info rounded-pill">Правильно: ${correct}/${total}</div>`;
        }
    });
    
    // Подсказка
    document.getElementById('hint-sudoku').addEventListener('click', function() {
        const inputs = document.querySelectorAll('#sudoku-grid input');
        const empty = [];
        inputs.forEach(input => {
            if (!input.readOnly && !input.value) {
                empty.push(input);
            }
        });
        
        if (empty.length > 0) {
            const random = empty[Math.floor(Math.random() * empty.length)];
            const row = parseInt(random.dataset.row);
            const col = parseInt(random.dataset.col);
            random.value = solution[row][col];
            random.classList.add('success');
            random.readOnly = true;
        } else {
            document.getElementById('sudoku-result').innerHTML = 
                '<div class="alert alert-warning rounded-pill">Нет пустых клеток!</div>';
        }
    });
    
    document.getElementById('new-sudoku').addEventListener('click', function() {
        document.getElementById('sudoku-result').innerHTML = '';
        renderGrid();
    });
    
    document.getElementById('difficulty').addEventListener('change', function() {
        document.getElementById('sudoku-result').innerHTML = '';
        renderGrid();
    });
    
    renderGrid();
});
</script>
@endpush