<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pembatas akses berbasis peran.
 *
 * Dipakai sebagai: ->middleware('role:admin') atau 'role:admin|owner'.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Versi sebelumnya langsung membaca auth()->user()->role. Bila
        // middleware ini sempat berjalan tanpa 'auth' di depannya, atau sesi
        // kedaluwarsa di tengah request, baris itu memicu error 500 dan
        // pesan errornya justru membocorkan jejak stack.
        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Dukung dua gaya penulisan: 'role:admin|owner' dan 'role:admin,owner'.
        $allowed = [];

        foreach ($roles as $role) {
            $allowed = array_merge($allowed, explode('|', $role));
        }

        $allowed = array_filter(array_map('trim', $allowed));

        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
