<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $role = $user?->role instanceof UserRole
            ? $user->role->value
            : (string) $user?->role;

        if (! $user || ! in_array($role, $roles)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
