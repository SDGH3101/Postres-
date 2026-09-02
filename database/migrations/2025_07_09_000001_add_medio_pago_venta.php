<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->enum('medio_pago', [
                'efectivo',
                'transferencia_nequi',
                'transferencia_daviplata',
                'transferencia_bancaria',
                'tarjeta_credito',
                'tarjeta_debito',
            ])->default('efectivo')->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn('medio_pago');
        });
    }
};
