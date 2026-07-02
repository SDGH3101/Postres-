<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── USUARIOS ──────────────────────────────────────────
        $lauraId = DB::table('usuario')->insertGetId([
            'nombre'     => 'Laura Martínez',
            'correo'     => 'laura@postres.com',
            'contrasena' => Hash::make('laura123'),
            'edad'       => 32,
            'registro'   => '2023-01-10',
        ]);
        $davidId = DB::table('usuario')->insertGetId([
            'nombre'     => 'David Gómez',
            'correo'     => 'david@postres.com',
            'contrasena' => Hash::make('david456'),
            'edad'       => 25,
            'registro'   => '2023-03-15',
        ]);
        $mariaId = DB::table('usuario')->insertGetId([
            'nombre'     => 'María López',
            'correo'     => 'maria@email.com',
            'contrasena' => Hash::make('maria789'),
            'edad'       => 28,
            'registro'   => '2023-06-01',
        ]);

        // ── ROLES ─────────────────────────────────────────────
        DB::table('emprendedor')->insert(['id_usuario' => $lauraId, 'descripcion' => 'Fundadora de Postres Laura']);
        DB::table('empleado')->insert(['id_usuario' => $davidId, 'cargo' => 'Pastelero', 'salario' => 1800000, 'horas_trabajadas' => 160]);
        DB::table('cliente')->insert(['id_usuario' => $mariaId]);

        // ── CATEGORÍAS ────────────────────────────────────────
        $cats = ['Lácteos','Harinas','Azúcares','Frutas','Chocolates','Esencias','Otros'];
        foreach ($cats as $c) {
            DB::table('categoria')->insert(['nombre_categoria' => $c]);
        }

        // ── PRODUCTOS ─────────────────────────────────────────
        $productos = [
            ['descripcion' => 'Torta de Chocolate',  'precio' => 85000,  'stock' => 12],
            ['descripcion' => 'Cupcakes x6',          'precio' => 32000,  'stock' => 30],
            ['descripcion' => 'Cheesecake de Fresas', 'precio' => 68000,  'stock' => 8],
            ['descripcion' => 'Macarons x12',         'precio' => 45000,  'stock' => 25],
            ['descripcion' => 'Brownie de Nuez',      'precio' => 28000,  'stock' => 40],
            ['descripcion' => 'Torta de Tres Leches', 'precio' => 75000,  'stock' => 6],
        ];
        foreach ($productos as $p) {
            DB::table('producto')->insert($p);
        }

        // ── VENTAS ────────────────────────────────────────────
        $ventas = [
            ['fecha' => '2024-10-05', 'total' => 85000,  'id_empleado' => $davidId, 'id_producto' => 1],
            ['fecha' => '2024-10-12', 'total' => 32000,  'id_empleado' => $davidId, 'id_producto' => 2],
            ['fecha' => '2024-11-03', 'total' => 68000,  'id_empleado' => $davidId, 'id_producto' => 3],
            ['fecha' => '2024-11-18', 'total' => 45000,  'id_empleado' => $davidId, 'id_producto' => 4],
            ['fecha' => '2024-12-02', 'total' => 85000,  'id_empleado' => $davidId, 'id_producto' => 1],
            ['fecha' => '2024-12-20', 'total' => 75000,  'id_empleado' => $davidId, 'id_producto' => 6],
            ['fecha' => '2025-01-08', 'total' => 28000,  'id_empleado' => $davidId, 'id_producto' => 5],
            ['fecha' => '2025-01-22', 'total' => 32000,  'id_empleado' => $davidId, 'id_producto' => 2],
        ];
        foreach ($ventas as $v) {
            DB::table('venta')->insert($v);
            DB::table('ingreso')->insert(['fecha' => $v['fecha'], 'monto' => $v['total'], 'concepto' => 'Venta', 'id_producto' => $v['id_producto']]);
        }

        // ── GASTOS ────────────────────────────────────────────
        DB::table('gasto')->insert([
            ['descripcion' => 'Luz y agua octubre', 'categoria' => 'Servicios', 'fecha' => '2024-10-01', 'monto' => 180000, 'presupuesto' => 200000, 'id_emprendedor' => $lauraId],
            ['descripcion' => 'Insumos noviembre',  'categoria' => 'Insumos',   'fecha' => '2024-11-01', 'monto' => 350000, 'presupuesto' => 400000, 'id_emprendedor' => $lauraId],
            ['descripcion' => 'Salario diciembre',  'categoria' => 'Nómina',    'fecha' => '2024-12-01', 'monto' => 1800000,'presupuesto' => 1800000,'id_emprendedor' => $lauraId],
        ]);

        // ── COMPRAS MENORES ───────────────────────────────────
        DB::table('compras_menores')->insert([
            ['descripcion' => 'Fresas 2kg',      'fecha' => '2024-10-10', 'monto' => 18000, 'id_emprendedor' => $lauraId, 'id_categoria' => 4],
            ['descripcion' => 'Harina 5kg',      'fecha' => '2024-11-05', 'monto' => 22000, 'id_emprendedor' => $lauraId, 'id_categoria' => 2],
            ['descripcion' => 'Chocolate amargo','fecha' => '2024-12-03', 'monto' => 35000, 'id_emprendedor' => $lauraId, 'id_categoria' => 5],
        ]);
    }
}
