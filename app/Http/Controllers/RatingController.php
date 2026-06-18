<?php

namespace App\Http\Controllers;

use App\Models\Polzovateli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $users = $this->getRatingQuery($request)->paginate(15);
        return view('rating.index', compact('users'));
    }

    public function search(Request $request)
    {
        $users = $this->getRatingQuery($request)->paginate(15);
        return view('rating.index', compact('users'));
    }

    private function getRatingQuery(Request $request)
    {
        $query = Polzovateli::query()
            ->select('polzovateli.*')
            ->selectRaw('COALESCE(sudoku.sum_ochki, 0) as sudoku_points')
            ->selectRaw('COALESCE(memory.sum_ochki, 0) as memory_points')
            ->selectRaw('COALESCE(snake.sum_ochki, 0) as snake_points')
            ->selectRaw('COALESCE(sudoku.sum_ochki, 0) + COALESCE(memory.sum_ochki, 0) + COALESCE(snake.sum_ochki, 0) as total_points')
            ->selectRaw('COALESCE(sudoku.count, 0) + COALESCE(memory.count, 0) + COALESCE(snake.count, 0) as games_played')
            ->leftJoin(DB::raw('(SELECT polzovatel_id, SUM(ochki) as sum_ochki, COUNT(*) as count FROM sudoku_rezultati GROUP BY polzovatel_id) as sudoku'), 'polzovateli.id', '=', 'sudoku.polzovatel_id')
            ->leftJoin(DB::raw('(SELECT polzovatel_id, SUM(ochki) as sum_ochki, COUNT(*) as count FROM naidi_paru_rezultati GROUP BY polzovatel_id) as memory'), 'polzovateli.id', '=', 'memory.polzovatel_id')
            ->leftJoin(DB::raw('(SELECT polzovatel_id, SUM(ochki) as sum_ochki, COUNT(*) as count FROM zmeyka_rezultati GROUP BY polzovatel_id) as snake'), 'polzovateli.id', '=', 'snake.polzovatel_id');

        // Поиск по имени/логину
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('login', 'LIKE', "%{$search}%")
                  ->orWhere('imya', 'LIKE', "%{$search}%");
            });
        }

        // Фильтр по игре
        if ($request->filled('game')) {
            switch ($request->game) {
                case 'sudoku':
                    $query->having('sudoku_points', '>', 0);
                    break;
                case 'memory':
                    $query->having('memory_points', '>', 0);
                    break;
                case 'snake':
                    $query->having('snake_points', '>', 0);
                    break;
            }
        }

        // Сортировка ТОЛЬКО по общим очкам (по убыванию)
        $query->orderByDesc('total_points');

        return $query;
    }
}