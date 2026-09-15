package com.example.demo.service;

import com.example.demo.modelo.Empleado;
import com.example.demo.modelo.Producto;
import com.example.demo.repository.EmpleadoRepository;
import com.example.demo.repository.GastoRepository;
import com.example.demo.repository.ProductoRepository;
import com.example.demo.repository.VentaRepository;
import com.example.demo.service.reporte.PeriodoConfig;
import com.example.demo.service.reporte.ReporteContext;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/**
 * Reportes para el modulo (equivale a ReporteController de Laravel - solo emprendedor).
 *
 * La configuracion de agrupacion/orden/filtro por periodo (diario, semanal, mensual, anual)
 * se delega a {@link ReporteContext}, que aplica el patron GoF - Strategy para seleccionar
 * la implementacion adecuada sin usar un switch/if-else en este servicio.
 */
@Service
public class ReporteService {

    private final JdbcTemplate jdbcTemplate;
    private final VentaRepository ventaRepository;
    private final GastoRepository gastoRepository;
    private final EmpleadoRepository empleadoRepository;
    private final ProductoRepository productoRepository;
    private final ReporteContext reporteContext;

    public ReporteService(JdbcTemplate jdbcTemplate,
                          VentaRepository ventaRepository,
                          GastoRepository gastoRepository,
                          EmpleadoRepository empleadoRepository,
                          ProductoRepository productoRepository,
                          ReporteContext reporteContext) {
        this.jdbcTemplate = jdbcTemplate;
        this.ventaRepository = ventaRepository;
        this.gastoRepository = gastoRepository;
        this.empleadoRepository = empleadoRepository;
        this.productoRepository = productoRepository;
        this.reporteContext = reporteContext;
    }

    public List<Map<String, Object>> ventasPorPeriodo(String periodo) {
        PeriodoConfig c = reporteContext.resolver(periodo);
        String sql = """
            SELECT %s AS mes, COUNT(v.id_venta) AS total_ventas,
                   SUM(v.total) AS ingresos_mes, AVG(v.total) AS ticket_promedio
            FROM venta v WHERE %s GROUP BY %s ORDER BY %s
        """.formatted(c.grupo(), c.limite(), c.grupo(), c.orden());
        return jdbcTemplate.queryForList(sql);
    }

    public List<Object[]> gastosPorCategoria() {
        return gastoRepository.gastosPorCategoria();
    }

    public List<Object[]> productosMasVendidos() {
        return ventaRepository.productosMasVendidos();
    }

    public List<Map<String, Object>> balanceVentasGastos(String periodo) {
        PeriodoConfig c = reporteContext.resolver(periodo);
        String sql = """
            SELECT ventas.mes, ventas.total_ventas AS ingresos,
                   COALESCE(gastos.total_gastos,0) AS gastos,
                   ventas.total_ventas - COALESCE(gastos.total_gastos,0) AS balance
            FROM (
                SELECT %s AS mes, SUM(total) AS total_ventas
                FROM venta WHERE %s GROUP BY %s
            ) ventas
            LEFT JOIN (
                SELECT %s AS mes, SUM(monto) AS total_gastos
                FROM gasto WHERE %s GROUP BY %s
            ) gastos ON ventas.mes = gastos.mes
            ORDER BY ventas.mes
        """.formatted(c.grupo(), c.limite(), c.grupo(), c.grupo(), c.limite(), c.grupo());
        return jdbcTemplate.queryForList(sql);
    }

    public List<Map<String, Object>> gastosDetallados() {
        String sql = """
            SELECT DATE_FORMAT(g.fecha,'%d-%m-%Y') AS fecha, g.descripcion,
                   g.categoria, g.monto, g.presupuesto
            FROM gasto g ORDER BY g.fecha
        """;
        return jdbcTemplate.queryForList(sql);
    }

    /**
     * Ventas detalladas con filtros multicriterio combinables: rango de fechas,
     * empleado y producto. Todos los parametros son opcionales (null = sin ese filtro).
     *
     * El WHERE se arma dinamicamente con StringBuilder, pero SIEMPRE con
     * placeholders "?" para los valores que vienen del usuario (nunca se
     * concatenan directamente en el SQL), evitando inyeccion SQL.
     */
    public List<Map<String, Object>> ventasFiltradas(LocalDate fechaInicio, LocalDate fechaFin,
                                                       Long idEmpleado, Long idProducto) {
        StringBuilder sql = new StringBuilder("""
            SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
                   u.nombre AS empleado,
                   GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS producto,
                   v.total
            FROM venta v
            JOIN empleado em ON v.id_empleado = em.id_usuario
            JOIN usuario u   ON em.id_usuario = u.id_usuario
            JOIN detalle_venta d ON d.id_venta = v.id_venta
            JOIN producto p      ON d.id_producto = p.id_producto
            WHERE 1=1
        """);
        List<Object> params = new ArrayList<>();

        if (fechaInicio != null) {
            sql.append(" AND v.fecha >= ? ");
            params.add(fechaInicio);
        }
        if (fechaFin != null) {
            sql.append(" AND v.fecha <= ? ");
            params.add(fechaFin);
        }
        if (idEmpleado != null) {
            sql.append(" AND em.id_usuario = ? ");
            params.add(idEmpleado);
        }
        if (idProducto != null) {
            // Filtra ventas que contengan ese producto en al menos una linea,
            // pero conservando en el resultado TODAS las lineas de esas ventas
            // (por eso va como subconsulta y no como condicion directa sobre d).
            sql.append(" AND v.id_venta IN (SELECT dv.id_venta FROM detalle_venta dv WHERE dv.id_producto = ?) ");
            params.add(idProducto);
        }

        sql.append(" GROUP BY v.id_venta, v.fecha, u.nombre, v.total ORDER BY v.fecha DESC ");
        return jdbcTemplate.queryForList(sql.toString(), params.toArray());
    }

    /** Empleados disponibles para el selector de filtro de reportes. */
    public List<Empleado> listaEmpleadosParaFiltro() {
        return empleadoRepository.findAll();
    }

    /** Productos disponibles para el selector de filtro de reportes. */
    public List<Producto> listaProductosParaFiltro() {
        return productoRepository.findAllByOrderByDescripcionAsc();
    }

    // ==========================================================================
    // BI / TABLERO ESTRATEGICO
    // ==========================================================================

    /**
     * Calcula los KPIs del tablero: ingresos, gastos, utilidad neta, margen de
     * ganancia (%) y ticket promedio. Respeta los filtros opcionales de rango de
     * fechas y de empleado (para las metricas que dependen de ventas).
     */
    public Map<String, Object> computarKpis(LocalDate fechaInicio, LocalDate fechaFin, Long idEmpleado) {
        BigDecimal ingresos = sumarVentas(fechaInicio, fechaFin, idEmpleado);
        BigDecimal gastos = sumarGastos(fechaInicio, fechaFin)
                .add(sumarComprasMenores(fechaInicio, fechaFin));
        BigDecimal utilidad = ingresos.subtract(gastos);
        BigDecimal margen = ingresos.signum() == 0
                ? BigDecimal.ZERO.setScale(2)
                : utilidad.multiply(BigDecimal.valueOf(100))
                          .divide(ingresos, 2, RoundingMode.HALF_UP);
        BigDecimal ticket = ticketPromedio(fechaInicio, fechaFin, idEmpleado);

        Map<String, Object> kpis = new LinkedHashMap<>();
        kpis.put("ingresos", ingresos);
        kpis.put("gastos", gastos);
        kpis.put("utilidad", utilidad);
        kpis.put("margen", margen);
        kpis.put("ticket", ticket);
        return kpis;
    }

    /** Suma de ventas con filtros opcionales (fechas y empleado). */
    private BigDecimal sumarVentas(LocalDate inicio, LocalDate fin, Long idEmpleado) {
        StringBuilder sql = new StringBuilder("SELECT COALESCE(SUM(v.total), 0) FROM venta v WHERE 1=1 ");
        List<Object> params = new ArrayList<>();
        if (inicio != null) { sql.append(" AND v.fecha >= ? "); params.add(inicio); }
        if (fin != null)     { sql.append(" AND v.fecha <= ? "); params.add(fin); }
        if (idEmpleado != null) { sql.append(" AND v.id_empleado = ? "); params.add(idEmpleado); }
        return jdbcTemplate.queryForObject(sql.toString(), BigDecimal.class, params.toArray());
    }

    /** Ticket promedio por venta con los mismos filtros opcionales. */
    private BigDecimal ticketPromedio(LocalDate inicio, LocalDate fin, Long idEmpleado) {
        StringBuilder sql = new StringBuilder("SELECT COALESCE(AVG(v.total), 0) FROM venta v WHERE 1=1 ");
        List<Object> params = new ArrayList<>();
        if (inicio != null) { sql.append(" AND v.fecha >= ? "); params.add(inicio); }
        if (fin != null)     { sql.append(" AND v.fecha <= ? "); params.add(fin); }
        if (idEmpleado != null) { sql.append(" AND v.id_empleado = ? "); params.add(idEmpleado); }
        return jdbcTemplate.queryForObject(sql.toString(), BigDecimal.class, params.toArray());
    }

    /** Suma de gastos con filtro opcional de rango de fechas. */
    private BigDecimal sumarGastos(LocalDate inicio, LocalDate fin) {
        StringBuilder sql = new StringBuilder("SELECT COALESCE(SUM(g.monto), 0) FROM gasto g WHERE 1=1 ");
        List<Object> params = new ArrayList<>();
        if (inicio != null) { sql.append(" AND g.fecha >= ? "); params.add(inicio); }
        if (fin != null)     { sql.append(" AND g.fecha <= ? "); params.add(fin); }
        return jdbcTemplate.queryForObject(sql.toString(), BigDecimal.class, params.toArray());
    }

    /** Suma de compras menores con filtro opcional de rango de fechas. */
    private BigDecimal sumarComprasMenores(LocalDate inicio, LocalDate fin) {
        StringBuilder sql = new StringBuilder("SELECT COALESCE(SUM(cm.monto), 0) FROM compras_menores cm WHERE 1=1 ");
        List<Object> params = new ArrayList<>();
        if (inicio != null) { sql.append(" AND cm.fecha >= ? "); params.add(inicio); }
        if (fin != null)     { sql.append(" AND cm.fecha <= ? "); params.add(fin); }
        return jdbcTemplate.queryForObject(sql.toString(), BigDecimal.class, params.toArray());
    }

    /**
     * Ventas por empleado (para el tablero): total de ventas, ingresos y ticket
     * promedio, ordenado por el empleado que mas factura.
     */
    public List<Map<String, Object>> ventasPorEmpleado() {
        String sql = """
            SELECT u.id_usuario, u.nombre AS empleado, em.cargo,
                   COUNT(v.id_venta) AS total_ventas,
                   COALESCE(SUM(v.total), 0) AS ingresos,
                   COALESCE(AVG(v.total), 0) AS ticket_promedio
            FROM empleado em
            JOIN usuario u ON em.id_usuario = u.id_usuario
            LEFT JOIN venta v ON v.id_empleado = em.id_usuario
            GROUP BY em.id_usuario, u.nombre, em.cargo
            ORDER BY ingresos DESC
        """;
        return jdbcTemplate.queryForList(sql);
    }

    /**
     * Productos sin rotacion (nunca vendidos): candidatos a promociones o a
     * retirar del catalogo. Trae stock y stock minimo para decidir.
     */
    public List<Map<String, Object>> productosSinRotacion() {
        String sql = """
            SELECT p.id_producto, p.descripcion, p.precio, p.stock, p.stock_minimo,
                   COALESCE(COUNT(d.id_detalle_venta), 0) AS veces_vendido
            FROM producto p
            LEFT JOIN detalle_venta d ON d.id_producto = p.id_producto
            GROUP BY p.id_producto, p.descripcion, p.precio, p.stock, p.stock_minimo
            HAVING COUNT(d.id_detalle_venta) = 0
            ORDER BY p.descripcion
        """;
        return jdbcTemplate.queryForList(sql);
    }
}
