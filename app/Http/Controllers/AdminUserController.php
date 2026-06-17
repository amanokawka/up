<?php

namespace App\Http\Controllers;

use App\Models\Polzovateli;
use App\Models\Bani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $users = Polzovateli::with('bani')
            ->orderBy('id')
            ->paginate(15);
        
        return view('admin.users', compact('users'));
    }

    public function search(Request $request)
    {
        $query = Polzovateli::with('bani');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('login', 'LIKE', "%{$search}%")
                  ->orWhere('imya', 'LIKE', "%{$search}%")
                  ->orWhereHas('rol', function($r) use ($search) {
                      $r->where('nazvanie', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $users = $query->orderBy('id')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function ban(Request $request, Polzovateli $user)
    {
        $request->validate([
            'prichina' => 'required|string|max:255',
        ]);

        // Деактивируем старые баны
        Bani::where('polzovatel_id', $user->id)
            ->where('aktiven', true)
            ->update(['aktiven' => false]);

        // Создаем новый бан
        Bani::create([
            'polzovatel_id' => $user->id,
            'moderator_id' => auth()->id(),
            'prichina' => $request->prichina,
            'aktiven' => true,
        ]);

        return redirect()->route('admin.users')
            ->with('success', "Пользователь {$user->login} заблокирован.");
    }

    public function unban(Polzovateli $user)
    {
        Bani::where('polzovatel_id', $user->id)
            ->where('aktiven', true)
            ->update(['aktiven' => false]);

        return redirect()->route('admin.users')
            ->with('success', "Пользователь {$user->login} разблокирован.");
    }

    public function resetStats(Request $request, Polzovateli $user)
    {
        $games = $request->input('games', []);
        
        if (in_array('all', $games)) {
            DB::transaction(function() use ($user) {
                DB::table('sudoku_rezultati')->where('polzovatel_id', $user->id)->delete();
                DB::table('naidi_paru_rezultati')->where('polzovatel_id', $user->id)->delete();
                DB::table('zmeyka_rezultati')->where('polzovatel_id', $user->id)->delete();
            });
            $message = "Вся статистика пользователя {$user->login} сброшена.";
        } else {
            foreach ($games as $game) {
                switch ($game) {
                    case 'sudoku':
                        DB::table('sudoku_rezultati')->where('polzovatel_id', $user->id)->delete();
                        break;
                    case 'memory':
                        DB::table('naidi_paru_rezultati')->where('polzovatel_id', $user->id)->delete();
                        break;
                    case 'snake':
                        DB::table('zmeyka_rezultati')->where('polzovatel_id', $user->id)->delete();
                        break;
                }
            }
            $message = "Статистика пользователя {$user->login} сброшена.";
        }

        return redirect()->route('admin.users')->with('success', $message);
    }

    public function destroy(Polzovateli $user)
    {
        // Только админ может удалять
        if (auth()->user()->rol_id != 3) {
            abort(403, 'Только администратор может удалять пользователей.');
        }

        // Не даем удалить самого себя
        if (auth()->id() == $user->id) {
            return redirect()->route('admin.users')
                ->with('error', 'Нельзя удалить самого себя.');
        }

        $login = $user->login;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Пользователь {$login} удалён.");
    }

    public function changeRole(Request $request, Polzovateli $user)
    {
        // Только админ может менять роли
        if (auth()->user()->rol_id != 3) {
            abort(403, 'Только администратор может менять роли.');
        }

        $request->validate([
            'rol_id' => 'required|in:1,2,3',
        ]);

        $user->update(['rol_id' => $request->rol_id]);

        return redirect()->route('admin.users')
            ->with('success', "Роль пользователя {$user->login} изменена.");
    }
}