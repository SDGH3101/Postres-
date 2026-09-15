package com.example.demo.service.reporte;

import org.springframework.stereotype.Component;

import java.util.Map;

/**
 * Contexto del patron GoF - Strategy: mantiene la referencia a todas las estrategias
 * de periodo disponibles (inyectadas automaticamente por Spring como un mapa
 * nombre-de-bean -&gt; estrategia, gracias a la anotacion @Component("diario"), etc. en
 * cada implementacion) y selecciona la adecuada segun el parametro "periodo" que llega
 * desde la capa de controlador, sin necesidad de un switch/if-else.
 *
 * Si el periodo solicitado no coincide con ninguna estrategia registrada (valor nulo,
 * vacio o desconocido), se usa "mensual" como estrategia por defecto.
 */
@Component
public class ReporteContext {

    private final Map<String, ReportePeriodoStrategy> estrategias;

    public ReporteContext(Map<String, ReportePeriodoStrategy> estrategias) {
        this.estrategias = estrategias;
    }

    public PeriodoConfig resolver(String periodo) {
        String clave = (periodo == null || periodo.isBlank()) ? "mensual" : periodo;
        ReportePeriodoStrategy estrategia = estrategias.getOrDefault(clave, estrategias.get("mensual"));
        return estrategia.calcular();
    }
}
