<?php

namespace App\Http\Controllers;

use App\Models\Polzovateli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Добро пожаловать!');
        }

        return back()->withErrors([
            'login' => 'Неверный логин или пароль.',
        ])->onlyInput('login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:50|unique:polzovateli',
            'imya' => 'nullable|string|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Polzovateli::create([
            'login' => $request->login,
            'imya' => $request->imya,
            'parol' => Hash::make($request->password),
            'rol_id' => 1,
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Добро пожаловать, ' . ($user->imya ?? $user->login) . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Вы вышли из аккаунта.');
    }
}