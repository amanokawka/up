@extends('layouts.app')

@section('title', 'Судоку')

@section('content')
<div class="text-center mb-4">
    <h1 class="page-title">🧩 Судоку</h1>
    <p class="page-subtitle">Заполните сетку цифрами от 1 до 9</p>
</div>

<div class="sudoku-wrapper">
    <div class="text-center mb-3">
        <select id="difficulty" class="form-control" style="max-width: 200px; margin: 0 auto; border-radius: 50px;">
            <option value="easy">😊 Легкий</option>
            <option value="medium" selected>🤔 Средний</option>
            <option value="hard">😈 Сложный</option>
        </select>
    </div>
    
    <div id="sudoku-grid" class="sudoku-grid">
        <!-- Генерируется JS -->
    </div>
    
    <div class="sudoku-controls">
        <button id="check-sudoku" class="btn btn-success">✅ Проверить</button>
        <button id="new-sudoku" class="btn btn-primary">🔄 Новая</button>
        <button id="hint-sudoku" class="btn btn-warning">💡 Подсказка</button>
    </div>
    
    <div id="sudoku-result" class="mt-3"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentBoard = [];
    let solution = [];
    let givenCells = [];
    let startTime = Date.now();
    let gameFinished = false;
    
    // Генерация судоку
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
        
        solveSudoku(board);
        solution = board.map(row => [...row]);
        
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
                            if (solveSudoku(board)) return true;
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
        gameFinished = false;
        startTime = Date.now();
        
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
        document.getElementById('sudoku-result').innerHTML = '';
    }
    
    function getScore() {
        const inputs = document.querySelectorAll('#sudoku-grid input');
        let correct = 0;
        let total = 0;
        let allFilled = true;
        
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
                    allFilled = false;
                } else {
                    allFilled = false;
                }
            }
        });
        
        return { correct, total, allFilled };
    }
    
    // Сохранение результата
    function saveResult(score, time) {
        const difficulty = document.getElementById('difficulty').value;
        
        fetch('{{ route("games.sudoku.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                slozhnost: difficulty,
                ochki: score,
                vremya: Math.floor(time / 1000)
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
    
    document.getElementById('check-sudoku').addEventListener('click', function() {
        if (gameFinished) return;
        
        const result = getScore();
        const resultDiv = document.getElementById('sudoku-result');
        const time = Date.now() - startTime;
        
        if (result.total === 0) {
            resultDiv.innerHTML = '<div class="alert alert-warning">Заполните хотя бы одну клетку!</div>';
            return;
        }
        
        if (result.allFilled && result.correct === result.total) {
            const score = result.correct * 10;
            resultDiv.innerHTML = `<div class="alert alert-success">🎉 Отлично! Все верно! Очки: ${score}</div>`;
            gameFinished = true;
            saveResult(score, time);
        } else {
            resultDiv.innerHTML = `<div class="alert alert-info">Правильно: ${result.correct}/${result.total}</div>`;
        }
    });
    
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
                '<div class="alert alert-warning">Нет пустых клеток!</div>';
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
@endsection