package com.example.demo.service;

import com.example.demo.repository.ComprasMenoresRepository;
import com.example.demo.repository.GastoRepository;
import com.example.demo.repository.IngresoRepository;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/**
 * Flujo de caja por periodo (equivale a FlujoCajaController de Laravel - solo emprendedor).
 * Usa JdbcTemplate para las consultas dinamicas de agrupacion por periodo.
 */
@Service
public class FlujoCajaService {

    private final JdbcTemplate jdbcTemplate;
    private final IngresoRepository ingresoRepository;
    private final GastoRepository gastoRepository;
    private final ComprasMenoresRepository comprasMenoresRepository;

    public FlujoCajaService(JdbcTemplate jdbcTemplate,
                            IngresoRepository ingresoRepository,
                            GastoRepository gastoRepository,
                            ComprasMenoresRepository comprasMenoresRepository) {
        this.jdbcTemplate = jdbcTemplate;
        this.ingresoRepository = ingresoRepository;
        this.gastoRepository = gastoRepository;
        this.comprasMenoresRepository = comprasMenoresRepository;
    }

    private static final Map<String, String[]> PERIODOS = Map.of(
        "diario",  new String[]{"DATE_FORMAT(fecha,'%d-%m-%Y')", "fecha", "fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"},
        "semanal", new String[]{"CONCAT('Sem ', WEEK(fecha,3), ' - ', YEAR(fecha))", "YEARWEEK(fecha,3)", "fecha >= DATE_SUB(CURDATE(), INTERVAL 16 WEEK)"},
        "mensual", new String[]{"DATE_FORMAT(fecha,'%m-%Y')", "DATE_FORMAT(fecha,'%Y-%m')", "fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)"},
        "anual",   new String[]{"YEAR(fecha)", "YEAR(fecha)", "1=1"}
    );

    public List<Map<String, Object>> flujoPorPeriodo(String periodo) {
        String[] cfg = PERIODOS.getOrDefault(periodo, PERIODOS.get("mensual"));
        String grupo = cfg[0];
        String orden = cfg[1];
        String limite = cfg[2];

        String sql = """
            SELECT periodo,
                   SUM(entradas) AS entradas,
                   SUM(salidas)  AS salidas,
                   SUM(entradas) - SUM(salidas) AS neto
            FROM (
                SELECT %s AS periodo, monto AS entradas, 0 AS salidas, %s AS ord
                FROM ingreso WHERE %s
                UNION ALL
                SELECT %s AS periodo, 0 AS entradas, monto AS salidas, %s AS ord
                FROM gasto WHERE %s
                UNION ALL
                SELECT %s AS periodo, 0 AS entradas, monto AS salidas, %s AS ord
                FROM compras_menores WHERE %s
            ) movimientos
            GROUP BY periodo, ord
            ORDER BY ord
        """.formatted(grupo, orden, limite, grupo, orden, limite, grupo, orden, limite);

        List<Map<String, Object>> filas = jdbcTemplate.queryForList(sql);

        BigDecimal acumulado = BigDecimal.ZERO;
        for (Map<String, Object> fila : filas) {
            BigDecimal neto = new BigDecimal(String.valueOf(fila.get("neto")));
            acumulado = acumulado.add(neto);
            fila.put("saldo_acumulado", acumulado);
        }
        return filas;
    }

    public Map<String, Object> saldoRealCaja() {
        BigDecimal ingresos = ingresoRepository.sumTotalMonto();
        BigDecimal gastos = gastoRepository.sumTotalMonto().add(comprasMenoresRepository.sumTotalMonto());
        Map<String, Object> m = new LinkedHashMap<>();
        m.put("totalIngresos", ingresos);
        m.put("totalGastos", gastos);
        m.put("saldoCaja", ingresos.subtract(gastos));
        return m;
    }

    public boolean esPeriodoValido(String periodo) {
        return periodo != null && PERIODOS.containsKey(periodo);
    }
}
