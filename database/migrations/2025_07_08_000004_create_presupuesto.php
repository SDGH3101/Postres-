<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuesto', function (Blueprint $table) {
            $table->id('id_presupuesto');
            $table->unsignedBigInteger('id_emprendedor');
            $table->enum('tipo', ['diario','semanal','mensual','anual']);
            $table->decimal('monto', 12, 2)->default(0);
            $table->date('fecha_inicio');
            $table->boolean('activo')->default(true);
            $table->timestamp('creado_en')->useCurrent();
            $table->foreign('id_emprendedor')->references('id_usuario')->on('emprendedor')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuesto');
    }
};
