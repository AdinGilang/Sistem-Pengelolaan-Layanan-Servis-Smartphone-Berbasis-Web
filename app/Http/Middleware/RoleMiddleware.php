<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Support single role: role:admin
     * Support multi role:  role:admin|owner
     */
    public function handle($request, Closure $next, $role)
    {
        $allowedRoles = explode('|', $role);

        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}