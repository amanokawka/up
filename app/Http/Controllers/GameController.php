<?php

namespace App\Http\Controllers;

use App\Models\SudokuRezultati;
use App\Models\NaidiParuRezultati;
use App\Models\ZmeykaRezultati;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    // ========== СТРАНИЦЫ ИГР ==========
    public function sudoku()
    {
        return view('games.sudoku');
    }

    public function memory()
    {
        return view('games.memory');
    }

    public function snake()
    {
        return view('games.snake');
    }

    // ========== СОХРАНЕНИЕ РЕЗУЛЬТАТОВ ==========

    // Сохранение результатов Судоку
    public function saveSudoku(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $request->validate([
            'slozhnost' => 'required|in:easy,medium,hard',
            'ochki' => 'required|integer',
            'vremya' => 'required|integer',
        ]);

        SudokuRezultati::create([
            'polzovatel_id' => Auth::id(),
            'slozhnost' => $request->slozhnost,
            'ochki' => $request->ochki,
            'vremya_sek' => $request->vremya,
            'zaversheno' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Результат сохранён!']);
    }

    // Сохранение результатов Найди пару
    public function saveMemory(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $request->validate([
            'pairs' => 'required|integer',
            'moves' => 'required|integer',
            'time' => 'required|integer',
            'score' => 'required|integer',
        ]);

        NaidiParuRezultati::create([
            'polzovatel_id' => Auth::id(),
            'kolichestvo_par' => $request->pairs,
            'hodi' => $request->moves,
            'vremya_sek' => $request->time,
            'ochki' => $request->score,
        ]);

        return response()->json(['success' => true, 'message' => 'Результат сохранён!']);
    }

    // Сохранение результатов Змейка
    public function saveSnake(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $request->validate([
            'length' => 'required|integer',
            'score' => 'required|integer',
            'time' => 'required|integer',
        ]);

        ZmeykaRezultati::create([
            'polzovatel_id' => Auth::id(),
            'dlina' => $request->length,
            'ochki' => $request->score,
            'vremya_sek' => $request->time,
        ]);

        return response()->json(['success' => true, 'message' => 'Результат сохранён!']);
    }
}