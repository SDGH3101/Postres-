<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\CompraApiController;
use App\Http\Controllers\Api\VentaApiController;

// API REST — Ítem 5 lista de chequeo (GET, POST, PUT, DELETE)
Route::prefix('v1')->group(function () {

    // ── Usuarios ──────────────────────────────────────────
    Route::get   ('usuarios',        [UsuarioApiController::class, 'index']);
    Route::get   ('usuarios/{id}',   [UsuarioApiController::class, 'show']);
    Route::post  ('usuarios',        [UsuarioApiController::class, 'store']);
    Route::put   ('usuarios/{id}',   [UsuarioApiController::class, 'update']);
    Route::delete('usuarios/{id}',   [UsuarioApiController::class, 'destroy']);

    // ── Productos ─────────────────────────────────────────
    Route::get   ('productos',       [ProductoApiController::class, 'index']);
    Route::get   ('productos/{id}',  [ProductoApiController::class, 'show']);
    Route::post  ('productos',       [ProductoApiController::class, 'store']);
    Route::put   ('productos/{id}',  [ProductoApiController::class, 'update']);
    Route::delete('productos/{id}',  [ProductoApiController::class, 'destroy']);

    // ── Compras/Gastos ────────────────────────────────────
    Route::get   ('compras',         [CompraApiController::class, 'index']);
    Route::post  ('compras',         [CompraApiController::class, 'store']);
    Route::delete('compras/{id}',    [CompraApiController::class, 'destroy']);

    // ── Ventas ────────────────────────────────────────────
    Route::get   ('ventas',          [VentaApiController::class, 'index']);
    Route::post  ('ventas',          [VentaApiController::class, 'store']);

    // ── Dashboard stats (Laravel) ─────────────────────────
    Route::get('stats', function () {
        $db = app('db');
        return response()->json([
            'total_productos' => $db->table('producto')->count(),
            'total_usuarios'  => $db->table('usuario')->count(),
            'total_ventas'    => $db->table('venta')->sum('total'),
            'total_gastos'    => $db->table('gasto')->sum('monto'),
        ]);
    });

    // ── Proxy al servicio Kotlin (interoperabilidad) ──────
    // GET /api/v1/kotlin/stats  → llama al microservicio Kotlin en :8081
    Route::get('kotlin/stats', function () {
        try {
            $response = Http::timeout(3)->get('http://localhost:8081/kotlin/stats');
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Servicio Kotlin no disponible',
                'detalle' => $e->getMessage(),
                'nota'    => 'Ejecuta: java -jar kotlin/stats.jar',
            ], 503);
        }
    });

    Route::get('kotlin/health', function () {
        try {
            $response = Http::timeout(2)->get('http://localhost:8081/kotlin/health');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => 'offline', 'error' => $e->getMessage()], 503);
        }
    });
});
