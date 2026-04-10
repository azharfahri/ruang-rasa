<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $hasRole = $user->roles()
            ->whereIn('name', $roles)
            ->exists();

        if (! $hasRole) {
            return abort(403, 'Anda tidak berhak akses halaman ini');
        }

        return $next($request);
    }
}

