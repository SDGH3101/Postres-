<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Corrige integridad al borrar una venta:
// 1) Antes: al borrar "venta" (cascade a detalle_venta) el stock descontado
//    por trg_descontar_stock NUNCA se restauraba, y el registro de "ingreso"
//    generado por trg_ingreso_por_venta quedaba huérfano (sin ningún vínculo
//    a la venta borrada).
// 2) Ahora: "ingreso" gana una columna id_detalle_venta con FK CASCADE hacia
//    detalle_venta, así que al borrarse la línea de venta, el ingreso
//    asociado se borra automáticamente. Y un nuevo trigger AFTER DELETE
//    restaura el stock del producto.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingreso', function (Blueprint $table) {
            $table->unsignedBigInteger('id_detalle_venta')->nullable()->after('id_producto');
            $table->foreign('id_detalle_venta')->references('id_detalle_venta')->on('detalle_venta')->onDelete('cascade');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS trg_ingreso_por_venta');
        DB::unprepared('
            CREATE TRIGGER trg_ingreso_por_venta
            AFTER INSERT ON detalle_venta
            FOR EACH ROW
            BEGIN
                INSERT INTO ingreso (fecha, monto, concepto, id_producto, id_detalle_venta)
                SELECT v.fecha, NEW.subtotal, \'Venta automática\', NEW.id_producto, NEW.id_detalle_venta
                FROM venta v WHERE v.id_venta = NEW.id_venta;
            END
        ');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_restaurar_stock_venta');
        DB::unprepared('
            CREATE TRIGGER trg_restaurar_stock_venta
            AFTER DELETE ON detalle_venta
            FOR EACH ROW
            BEGIN
                UPDATE producto
                SET stock = stock + OLD.cantidad
                WHERE id_producto = OLD.id_producto;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_restaurar_stock_venta');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_ingreso_por_venta');
        DB::unprepared('
            CREATE TRIGGER trg_ingreso_por_venta
            AFTER INSERT ON detalle_venta
            FOR EACH ROW
            BEGIN
                INSERT INTO ingreso (fecha, monto, concepto, id_producto)
                SELECT v.fecha, NEW.subtotal, \'Venta automática\', NEW.id_producto
                FROM venta v WHERE v.id_venta = NEW.id_venta;
            END
        ');

        Schema::table('ingreso', function (Blueprint $table) {
            $table->dropForeign(['id_detalle_venta']);
            $table->dropColumn('id_detalle_venta');
        });
    }
};
