package com.example.demo.service.reporte;

import org.springframework.stereotype.Component;

/**
 * Estrategia concreta (GoF - Strategy) para reportes agrupados por dia.
 * Cubre los ultimos 30 dias.
 */
@Component("diario")
public class ReporteDiarioStrategy implements ReportePeriodoStrategy {

    @Override
    public PeriodoConfig calcular() {
        return new PeriodoConfig(
            "DATE_FORMAT(fecha,'%d-%m-%Y')",
            "fecha",
            "fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
        );
    }
}
