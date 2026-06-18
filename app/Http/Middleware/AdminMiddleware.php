<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ✅ rol_id: 1 - пользователь, 2 - модератор, 3 - администратор
        // Доступ для модератора (2) и администратора (3)
        if (Auth::user()->rol_id == 2 || Auth::user()->rol_id == 3) {
            return $next($request);
        }

        abort(403, 'Доступ запрещен. Требуются права модератора или администратора.');
    }
}