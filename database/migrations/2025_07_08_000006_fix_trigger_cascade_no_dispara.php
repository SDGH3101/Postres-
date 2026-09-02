<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Corrige la migración anterior (2025_07_08_000005): MySQL/MariaDB NO dispara
// triggers AFTER DELETE en una tabla hija cuando la fila se borra por un
// ON DELETE CASCADE de foreign key (limitación documentada y conocida desde
// hace años, ver bugs.mysql.com/bug.php?id=11472). Por eso
// trg_restaurar_stock_venta nunca se ejecutaba al borrar una "venta": el
// borrado de "detalle_venta" ocurría por cascada de FK, no por un DELETE
// explícito.
// Solución: un trigger BEFORE DELETE en "venta" que borra explícitamente
// sus líneas de detalle_venta ANTES de que la fila padre se elimine. Al ser
// un DELETE explícito (no una cascada), sí dispara trg_restaurar_stock_venta
// (restaura stock) y, por FK cascade normal, también borra el ingreso
// asociado.
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_borrar_venta');
        DB::unprepared('
            CREATE TRIGGER trg_before_borrar_venta
            BEFORE DELETE ON venta
            FOR EACH ROW
            BEGIN
                DELETE FROM detalle_venta WHERE id_venta = OLD.id_venta;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_before_borrar_venta');
    }
};
