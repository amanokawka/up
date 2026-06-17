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

        // rol_id: 1 - user, 2 - moderator, 3 - admin
        if (Auth::user()->rol_id > 2) {
            return $next($request);
        }

        abort(403, 'Доступ запрещен. Требуются права администратора или модератора.');
    }
}