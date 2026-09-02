<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('usuario')) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'pass'   => 'required',
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email'    => 'Ingresa un correo válido.',
            'pass.required'   => 'La contraseña es obligatoria.',
        ]);

        $usuario = DB::table('usuario')->where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->pass, $usuario->contrasena)) {
            return back()->withErrors(['correo' => 'Correo o contraseña incorrectos.'])->withInput();
        }

        // Detectar rol
        $rol = 'cliente';
        if (DB::table('emprendedor')->where('id_usuario', $usuario->id_usuario)->exists()) $rol = 'emprendedor';
        elseif (DB::table('empleado')->where('id_usuario', $usuario->id_usuario)->exists()) $rol = 'empleado';

        session(['usuario' => [
            'id'     => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'rol'    => $rol,
        ]]);

        return redirect()->route('dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'correo'           => 'required|email|unique:usuario,correo',
            'fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(16)->toDateString() . '|after:' . now()->subYears(99)->toDateString(),
            'pass'             => 'required|min:6|confirmed',
        ], [
            'correo.unique'                 => 'Ese correo ya está registrado.',
            'pass.confirmed'                => 'Las contraseñas no coinciden.',
            'pass.min'                      => 'Mínimo 6 caracteres.',
            'fecha_nacimiento.required'     => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 16 años para registrarte.',
            'fecha_nacimiento.after'        => 'Ingresa una fecha de nacimiento válida.',
        ]);

        $edad = \Carbon\Carbon::parse($request->fecha_nacimiento)->age;

        $id = DB::table('usuario')->insertGetId([
            'nombre'           => $request->nombre,
            'correo'           => $request->correo,
            'edad'             => $edad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'contrasena'       => Hash::make($request->pass),
            'registro'         => now()->toDateString(),
        ]);

        DB::table('cliente')->insert(['id_usuario' => $id]);

        return redirect()->route('login')->with('success', '¡Cuenta creada! Ya puedes ingresar.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('usuario');
        return redirect()->route('login');
    }
}
