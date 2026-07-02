<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;

// Rutas públicas de autenticación
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren sesión)
Route::middleware('auth.session')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catálogo público para todos los roles
    Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('catalogo');

    // ── Solo emprendedor y empleado
    Route::middleware('role:emprendedor,empleado')->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::resource('compras',   CompraController::class);
        Route::resource('ventas',    VentaController::class);
    });

    // ── Solo emprendedor
    Route::middleware('role:emprendedor')->group(function () {
        Route::resource('usuarios',  UsuarioController::class);
        Route::resource('empleados', EmpleadoController::class);

        // Reportes exportables
        Route::get('/reportes',           [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/ventas/pdf',[ReporteController::class, 'ventasPdf'])->name('reportes.ventas.pdf');
        Route::get('/reportes/gastos/pdf',[ReporteController::class, 'gastosPdf'])->name('reportes.gastos.pdf');
        Route::get('/reportes/excel',     [ReporteController::class, 'excel'])->name('reportes.excel');
    });
});
