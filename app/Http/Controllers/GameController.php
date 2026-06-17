<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
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
}