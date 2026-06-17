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
        $this->middleware('auth')->except(['index']);
    }

    public function index()
    {
        if (!Auth::check()) {
            return view('support.guest');
        }
        
        $tickets = TiketiPodderzhki::where('polzovatel_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('support.index', compact('tickets'));
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

        return redirect()->route('support.show', $ticket->id)
            ->with('success', 'Тикет создан успешно!');
    }

    public function show(TiketiPodderzhki $ticket)
    {
        // Проверяем, что пользователь владелец тикета или модератор/админ
        if (Auth::id() != $ticket->polzovatel_id && Auth::user()->rol_id > 2) {
            abort(403, 'У вас нет доступа к этому тикету.');
        }

        $ticket->load('soobsheniya.polzovatel', 'moderator');
        return view('support.show', compact('ticket'));
    }

    public function message(Request $request, TiketiPodderzhki $ticket)
    {
        $request->validate([
            'tekst' => 'required|string',
        ]);

        $isStaff = Auth::user()->rol_id <= 2; // admin или moderator
        
        // Проверяем доступ
        if (Auth::id() != $ticket->polzovatel_id && !$isStaff) {
            abort(403);
        }

        // Если модератор отвечает - меняем статус
        if ($isStaff && $ticket->status == 'open') {
            $ticket->update(['status' => 'in_progress', 'moderator_id' => Auth::id()]);
        }

        SoobsheniyaTicketov::create([
            'ticket_id' => $ticket->id,
            'polzovatel_id' => Auth::id(),
            'tekst' => $request->tekst,
            'ot_personala' => $isStaff,
        ]);

        return redirect()->route('support.show', $ticket->id)
            ->with('success', 'Сообщение отправлено!');
    }
}