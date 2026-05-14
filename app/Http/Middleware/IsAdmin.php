<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No tienes permisos de administrador',
                ], 403);
            }
            abort(403, 'Acceso restringido a administradores.');
        }

        return $next($request);
    }
}
