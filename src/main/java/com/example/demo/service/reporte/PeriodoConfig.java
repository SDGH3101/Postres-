package com.example.demo.service.reporte;

/**
 * Value object con la configuracion SQL necesaria para agrupar, ordenar y filtrar
 * las consultas de reportes segun el periodo elegido (diario, semanal, mensual, anual).
 *
 * @param grupo  expresion SQL usada en el SELECT/GROUP BY para etiquetar cada fila
 * @param orden  expresion SQL usada en el ORDER BY (puede diferir del grupo, p.ej. para
 *               ordenar cronologicamente aunque la etiqueta visible sea un texto)
 * @param limite condicion SQL (WHERE) que acota el rango temporal del reporte
 */
public record PeriodoConfig(String grupo, String orden, String limite) {
}
