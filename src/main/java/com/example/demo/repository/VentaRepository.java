package com.example.demo.repository;

import com.example.demo.modelo.Venta;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface VentaRepository extends JpaRepository<Venta, Long> {

    @Query("select count(v) from Venta v where v.empleado.idUsuario = :idEmpleado")
    long countVentasDeEmpleado(@Param("idEmpleado") Long idEmpleado);

    /**
     * Lista ventas con sus lineas agrupadas (equivale a sp_listar_ventas).
     * [0]=id, [1]=fecha(dd-mm-YYYY), [2]=empleado, [3]=cargo, [4]=total, [5]=medio_pago, [6]=productos(GROUP_CONCAT)
     */
    @Query(value = """
        SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
               u.nombre AS empleado, em.cargo, v.total, v.medio_pago,
               GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS productos
        FROM venta v
        JOIN empleado em ON v.id_empleado = em.id_usuario
        JOIN usuario  u  ON em.id_usuario = u.id_usuario
        JOIN detalle_venta d ON d.id_venta = v.id_venta
        JOIN producto p  ON d.id_producto = p.id_producto
        GROUP BY v.id_venta, v.fecha, u.nombre, em.cargo, v.total, v.medio_pago
        ORDER BY v.fecha DESC
    """, nativeQuery = true)
    List<Object[]> listarVentas();

    /**
     * Ventas por mes para grafica (sp_ventas_por_mes).
     * [0]=mes, [1]=total_ventas, [2]=ingresos_mes, [3]=ticket_promedio
     */
    @Query(value = """
        SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes,
               COUNT(*) AS total_ventas,
               SUM(total) AS ingresos_mes,
               AVG(total) AS ticket_promedio
        FROM venta
        GROUP BY DATE_FORMAT(fecha,'%m-%Y')
        ORDER BY mes
    """, nativeQuery = true)
    List<Object[]> ventasPorMes();

    /**
     * Ultimas 5 ventas para el dashboard.
     * [0]=id, [1]=fecha, [2]=total, [3]=empleado, [4]=productos
     */
    @Query(value = """
        SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha, v.total,
               u.nombre AS empleado,
               GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS producto
        FROM venta v
        JOIN detalle_venta d ON d.id_venta = v.id_venta
        JOIN producto p ON d.id_producto = p.id_producto
        JOIN empleado em ON v.id_empleado = em.id_usuario
        JOIN usuario  u  ON em.id_usuario = u.id_usuario
        GROUP BY v.id_venta, v.fecha, v.total, u.nombre
        ORDER BY v.fecha DESC LIMIT 5
    """, nativeQuery = true)
    List<Object[]> ultimasVentas();

    /**
     * Productos mas vendidos (sp_productos_mas_vendidos).
     * [0]=descripcion, [1]=precio, [2]=stock, [3]=veces, [4]=ingresos
     */
    @Query(value = """
        SELECT p.descripcion, p.precio, p.stock,
               COUNT(d.id_detalle_venta) AS veces,
               SUM(d.subtotal) AS ingresos
        FROM producto p
        JOIN detalle_venta d ON d.id_producto = p.id_producto
        GROUP BY p.id_producto, p.descripcion, p.precio, p.stock
        ORDER BY veces DESC LIMIT 5
    """, nativeQuery = true)
    List<Object[]> productosMasVendidos();
}
