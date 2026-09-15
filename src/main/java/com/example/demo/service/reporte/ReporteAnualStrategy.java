package com.example.demo.service.reporte;

import org.springframework.stereotype.Component;

/**
 * Estrategia concreta (GoF - Strategy) para reportes agrupados por anio, sin limite
 * de rango temporal (se incluye todo el historico de ventas/gastos).
 */
@Component("anual")
public class ReporteAnualStrategy implements ReportePeriodoStrategy {

    @Override
    public PeriodoConfig calcular() {
        return new PeriodoConfig("YEAR(fecha)", "YEAR(fecha)", "1=1");
    }
}
