<?php

namespace App\Http\Controllers;

use App\Models\Polzovateli;
use App\Models\TiketiPodderzhki;
use App\Models\Bani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $stats = [
            'total_users' => Polzovateli::count(),
            'open_tickets' => TiketiPodderzhki::where('status', 'open')->count(),
            'banned_users' => Bani::where('aktiven', true)->distinct('polzovatel_id')->count(),
            'total_games' => DB::table('sudoku_rezultati')->count() +
                            DB::table('naidi_paru_rezultati')->count() +
                            DB::table('zmeyka_rezultati')->count(),
            'recent_tickets' => TiketiPodderzhki::with('polzovatel')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'top_player' => $this->getTopPlayer(),
        ];

        return view('admin.index', compact('stats'));
    }

    private function getTopPlayer()
    {
        $topPlayer = Polzovateli::select('polzovateli.*')
            ->selectRaw('COALESCE(sudoku.sum_ochki, 0) + COALESCE(memory.sum_ochki, 0) + COALESCE(snake.sum_ochki, 0) as total_points')
            ->leftJoin(DB::raw('(SELECT polzovatel_id, SUM(ochki) as sum_ochki FROM sudoku_rezultati GROUP BY polzovatel_id) as sudoku'), 'polzovateli.id', '=', 'sudoku.polzovatel_id')
            ->leftJoin(DB::raw('(SELECT polzovatel_id, SUM(ochki) as sum_ochki FROM naidi_paru_rezultati GROUP BY polzovatel_id) as memory'), 'polzovateli.id', '=', 'memory.polzovatel_id')
            ->leftJoin(DB::raw('(SELECT polzovatel_id, SUM(ochki) as sum_ochki FROM zmeyka_rezultati GROUP BY polzovatel_id) as snake'), 'polzovateli.id', '=', 'snake.polzovatel_id')
            ->orderByDesc('total_points')
            ->first();

        return $topPlayer ? ($topPlayer->imya ?? $topPlayer->login) : null;
    }
}