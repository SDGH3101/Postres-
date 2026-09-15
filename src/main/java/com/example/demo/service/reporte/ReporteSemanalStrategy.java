package com.example.demo.service.reporte;

import org.springframework.stereotype.Component;

/**
 * Estrategia concreta (GoF - Strategy) para reportes agrupados por semana ISO.
 * Cubre las ultimas 16 semanas.
 */
@Component("semanal")
public class ReporteSemanalStrategy implements ReportePeriodoStrategy {

    @Override
    public PeriodoConfig calcular() {
        return new PeriodoConfig(
            "CONCAT('Sem ', WEEK(fecha,3), ' - ', YEAR(fecha))",
            "YEARWEEK(fecha,3)",
            "fecha >= DATE_SUB(CURDATE(), INTERVAL 16 WEEK)"
        );
    }
}
