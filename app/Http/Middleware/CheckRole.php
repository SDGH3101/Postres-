<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $usuario = session('usuario');
        if (!$usuario || !in_array($usuario['rol'], $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Acceso denegado'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para acceder a esa sección.');
        }
        return $next($request);
    }
}
