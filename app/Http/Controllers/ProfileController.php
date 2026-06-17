<?php

namespace App\Http\Controllers;

use App\Models\Polzovateli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user()->load(['sudokuRezultati', 'naidiParuRezultati', 'zmeykaRezultati']);
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'imya' => 'nullable|string|max:100',
            'login' => 'required|string|max:50|unique:polzovateli,login,' . $user->id,
        ]);

        $user->update([
            'imya' => $request->imya,
            'login' => $request->login,
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Профиль обновлён успешно!');
    }

    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::user();
        
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => '/storage/' . $path]);

        return redirect()->route('profile.index')
            ->with('success', 'Аватар обновлён!');
    }
}