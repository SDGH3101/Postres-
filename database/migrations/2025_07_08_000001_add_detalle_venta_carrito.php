<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Nueva tabla: una venta puede tener muchas líneas de producto
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->id('id_detalle_venta');
            $table->unsignedBigInteger('id_venta');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->foreign('id_venta')->references('id_venta')->on('venta')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('producto')->onDelete('restrict');
        });

        // 2. Migrar ventas existentes (1 producto) a detalle_venta (cantidad = 1)
        $ventasViejas = DB::table('venta')->whereNotNull('id_producto')->get();
        foreach ($ventasViejas as $v) {
            DB::table('detalle_venta')->insert([
                'id_venta'        => $v->id_venta,
                'id_producto'     => $v->id_producto,
                'cantidad'        => 1,
                'precio_unitario' => $v->total,
                'subtotal'        => $v->total,
            ]);
        }

        // 3. Quitar la FK y la columna id_producto de venta (ya no es 1 producto por venta)
        //    La tabla se creó con SQL crudo (no con migraciones de Laravel), así que el
        //    nombre de la FK sigue la convención de MySQL (ej: venta_ibfk_2), no la de
        //    Laravel (venta_id_producto_foreign). Lo buscamos dinámicamente.
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'venta'
              AND COLUMN_NAME = 'id_producto'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if ($fk) {
            DB::statement("ALTER TABLE venta DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn('id_producto');
        });

        // 4. Los triggers viejos vivían en "venta" (1 producto). Los quitamos y
        //    los recreamos sobre "detalle_venta", que es donde ahora vive cada línea.
        DB::unprepared('DROP TRIGGER IF EXISTS trg_descontar_stock');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_ingreso_por_venta');

        DB::unprepared('
            CREATE TRIGGER trg_descontar_stock
            AFTER INSERT ON detalle_venta
            FOR EACH ROW
            BEGIN
                UPDATE producto
                SET stock = stock - NEW.cantidad
                WHERE id_producto = NEW.id_producto;
            END
        ');

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
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_descontar_stock');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_ingreso_por_venta');

        // Nota: si una venta llegó a tener varios productos, el rollback solo
        // puede recuperar el primero (limitación esperada al revertir).
        Schema::table('venta', function (Blueprint $table) {
            $table->unsignedBigInteger('id_producto')->nullable()->after('total');
        });

        $primeros = DB::table('detalle_venta')
            ->select('id_venta', DB::raw('MIN(id_detalle_venta) as primer_detalle'))
            ->groupBy('id_venta')->get();

        foreach ($primeros as $row) {
            $detalle = DB::table('detalle_venta')->find($row->primer_detalle);
            DB::table('venta')->where('id_venta', $row->id_venta)
                ->update(['id_producto' => $detalle->id_producto]);
        }

        Schema::table('venta', function (Blueprint $table) {
            $table->foreign('id_producto')->references('id_producto')->on('producto')->onDelete('restrict');
        });

        DB::unprepared('
            CREATE TRIGGER trg_descontar_stock
            AFTER INSERT ON venta
            FOR EACH ROW
            BEGIN
                UPDATE producto SET stock = stock - 1 WHERE id_producto = NEW.id_producto;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER trg_ingreso_por_venta
            AFTER INSERT ON venta
            FOR EACH ROW
            BEGIN
                INSERT INTO ingreso (fecha, monto, concepto, id_producto)
                VALUES (NEW.fecha, NEW.total, \'Venta automática\', NEW.id_producto);
            END
        ');

        Schema::dropIfExists('detalle_venta');
    }
};
