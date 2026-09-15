package com.example.demo.service.reporte;

/**
 * @pattern GoF - Strategy Pattern para el calculo dinamico de reportes por periodo.
 *
 * Cada implementacion encapsula el algoritmo de agrupacion/orden/filtro SQL asociado
 * a un periodo de reporte especifico (diario, semanal, mensual, anual), permitiendo
 * que {@link ReporteContext} (el "contexto" del patron) delegue el calculo sin conocer
 * los detalles de cada periodo ni recurrir a un switch/if-else.
 *
 * Las implementaciones se registran como beans de Spring nombrados exactamente igual
 * al valor del parametro "periodo" que llega desde el controlador (ver @Component("diario"),
 * etc.), lo que permite a Spring inyectar automaticamente un Map&lt;String, ReportePeriodoStrategy&gt;
 * con todas las estrategias disponibles.
 */
public interface ReportePeriodoStrategy {

    /**
     * Calcula la configuracion SQL (agrupacion, orden y filtro) para este periodo.
     *
     * @return la configuracion lista para usarse en las consultas de ReporteService
     */
    PeriodoConfig calcular();
}
