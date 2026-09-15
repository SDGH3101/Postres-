package com.example.demo.service.reporte;

import org.springframework.stereotype.Component;

/**
 * Estrategia concreta (GoF - Strategy) para reportes agrupados por mes.
 * Es la estrategia por defecto cuando no se especifica un periodo valido.
 * Cubre los ultimos 12 meses.
 */
@Component("mensual")
public class ReporteMensualStrategy implements ReportePeriodoStrategy {

    @Override
    public PeriodoConfig calcular() {
        return new PeriodoConfig(
            "DATE_FORMAT(fecha,'%m-%Y')",
            "DATE_FORMAT(fecha,'%Y-%m')",
            "fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)"
        );
    }
}
