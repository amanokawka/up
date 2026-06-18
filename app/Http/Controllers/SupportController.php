<?php

namespace App\Http\Controllers;

use App\Models\TiketiPodderzhki;
use App\Models\SoobsheniyaTicketov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $isStaff = $user->rol_id == 2 || $user->rol_id == 3;

        // Мои тикеты (для всех)
        $myTickets = TiketiPodderzhki::where('polzovatel_id', $user->id)
            ->with(['polzovatel', 'soobsheniya'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'my_page');

        // Открытые тикеты (только для модератора/админа)
        if ($isStaff) {
            $openTickets = TiketiPodderzhki::whereIn('status', ['open', 'in_progress'])
                ->where('polzovatel_id', '!=', $user->id) // не показываем свои открытые
                ->with(['polzovatel', 'soobsheniya'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'open_page');
        } else {
            $openTickets = collect(); // пустая коллекция
        }

        return view('support.index', compact('myTickets', 'openTickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tema' => 'required|string|max:255',
            'tekst' => 'required|string',
        ]);

        $ticket = TiketiPodderzhki::create([
            'polzovatel_id' => Auth::id(),
            'tema' => $request->tema,
            'status' => 'open',
        ]);

        SoobsheniyaTicketov::create([
            'ticket_id' => $ticket->id,
            'polzovatel_id' => Auth::id(),
            'tekst' => $request->tekst,
            'ot_personala' => false,
        ]);

        return redirect()->route('support.index')
            ->with('success', 'Тикет создан успешно!');
    }

    public function show(TiketiPodderzhki $ticket)
    {
        $user = Auth::user();
        $isStaff = $user->rol_id == 2 || $user->rol_id == 3;
        $isOwner = $user->id == $ticket->polzovatel_id;

        if (!$isStaff && !$isOwner) {
            abort(403, 'У вас нет доступа к этому тикету.');
        }

        $ticket->load(['soobsheniya.polzovatel', 'moderator', 'polzovatel']);
        return view('support.show', compact('ticket'));
    }

    public function message(Request $request, TiketiPodderzhki $ticket)
    {
        $request->validate([
            'tekst' => 'required|string',
        ]);

        $user = Auth::user();
        $isStaff = $user->rol_id == 2 || $user->rol_id == 3;
        $isOwner = $user->id == $ticket->polzovatel_id;

        if (!$isStaff && !$isOwner) {
            abort(403, 'У вас нет доступа к этому тикету.');
        }

        if ($ticket->status == 'closed') {
            return redirect()->route('support.show', $ticket->id)
                ->with('error', 'Тикет закрыт.');
        }

        if ($isStaff && $ticket->status == 'open') {
            $ticket->update([
                'status' => 'in_progress',
                'moderator_id' => $user->id
            ]);
        }

        SoobsheniyaTicketov::create([
            'ticket_id' => $ticket->id,
            'polzovatel_id' => $user->id,
            'tekst' => $request->tekst,
            'ot_personala' => $isStaff,
        ]);

        return redirect()->route('support.show', $ticket->id)
            ->with('success', 'Сообщение отправлено!');
    }

    public function close(TiketiPodderzhki $ticket)
    {
        $user = Auth::user();

        if ($user->rol_id == 1) {
            return response()->json(['error' => 'Доступ запрещён'], 403);
        }

        if ($ticket->status == 'closed') {
            return response()->json(['error' => 'Тикет уже закрыт'], 400);
        }

        $ticket->update([
            'status' => 'closed',
            'moderator_id' => $user->id
        ]);

        return response()->json(['success' => true]);
    }
}