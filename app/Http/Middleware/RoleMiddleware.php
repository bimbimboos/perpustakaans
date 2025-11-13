<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Normalisasi role jadi lowercase
        $userRole = strtolower(trim($user->role ?? ''));
        $roles = array_map(fn($r) => strtolower(trim($r)), $roles);

        // ⬇️ FIX INI! Pakai $userRole bukan $user->role
        if (!in_array($userRole, $roles)) {
            abort(403, "Unauthorized. User role: {$userRole}, Allowed: " . implode(',', $roles));
        }

        return $next($request);
    }
}
