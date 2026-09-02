<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. USUARIO
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre', 100);
            $table->string('correo', 100)->unique();
            $table->string('contrasena', 255);
            $table->unsignedTinyInteger('edad')->nullable();
            $table->date('registro')->nullable();
        });

        // 2. EMPRENDEDOR
        Schema::create('emprendedor', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->primary();
            $table->string('descripcion', 300)->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });

        // 3. EMPLEADO
        Schema::create('empleado', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->primary();
            $table->string('cargo', 80)->nullable();
            $table->decimal('salario', 12, 2)->default(0);
            $table->unsignedSmallInteger('horas_trabajadas')->default(0);
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });

        // 4. CLIENTE
        Schema::create('cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->primary();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });

        // 5. CATEGORIA
        Schema::create('categoria', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->string('nombre_categoria', 80);
        });

        // 6. PRODUCTO
        Schema::create('producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('descripcion', 200);
            $table->decimal('precio', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
        });

        // 7. VENTA
        Schema::create('venta', function (Blueprint $table) {
            $table->id('id_venta');
            $table->date('fecha');
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_producto');
            $table->foreign('id_empleado')->references('id_usuario')->on('empleado')->onDelete('restrict');
            $table->foreign('id_producto')->references('id_producto')->on('producto')->onDelete('restrict');
        });

        // 8. INGRESO (generado por trigger al registrar venta)
        Schema::create('ingreso', function (Blueprint $table) {
            $table->id('id_ingreso');
            $table->date('fecha');
            $table->decimal('monto', 12, 2)->default(0);
            $table->string('concepto', 200)->nullable();
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->foreign('id_producto')->references('id_producto')->on('producto')->onDelete('set null');
        });

        // 9. GASTO
        Schema::create('gasto', function (Blueprint $table) {
            $table->id('id_gasto');
            $table->string('descripcion', 200);
            $table->string('categoria', 80)->default('Otros');
            $table->date('fecha');
            $table->decimal('monto', 12, 2)->default(0);
            $table->decimal('presupuesto', 12, 2)->default(0);
            $table->unsignedBigInteger('id_emprendedor');
            $table->foreign('id_emprendedor')->references('id_usuario')->on('emprendedor')->onDelete('restrict');
        });

        // 10. COMPRAS_MENORES
        Schema::create('compras_menores', function (Blueprint $table) {
            $table->id('id_compra');
            $table->string('descripcion', 200);
            $table->date('fecha');
            $table->decimal('monto', 12, 2)->default(0);
            $table->unsignedBigInteger('id_emprendedor');
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->foreign('id_emprendedor')->references('id_usuario')->on('emprendedor')->onDelete('restrict');
            $table->foreign('id_categoria')->references('id_categoria')->on('categoria')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras_menores');
        Schema::dropIfExists('gasto');
        Schema::dropIfExists('ingreso');
        Schema::dropIfExists('venta');
        Schema::dropIfExists('producto');
        Schema::dropIfExists('categoria');
        Schema::dropIfExists('cliente');
        Schema::dropIfExists('empleado');
        Schema::dropIfExists('emprendedor');
        Schema::dropIfExists('usuario');
    }
};
