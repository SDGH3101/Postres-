<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->date('fecha_caducidad')->nullable()->after('stock');
            $table->unsignedInteger('stock_minimo')->default(5)->after('fecha_caducidad');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn(['fecha_caducidad', 'stock_minimo']);
        });
    }
};
