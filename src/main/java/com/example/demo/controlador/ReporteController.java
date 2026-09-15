package com.example.demo.controlador;

import com.example.demo.service.ReporteService;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

import java.nio.charset.StandardCharsets;
import java.time.LocalDate;

/**
 * Reportes (equivale a ReporteController de Laravel - solo emprendedor).
 */
@Controller
public class ReporteController {

    private final ReporteService reporteService;

    public ReporteController(ReporteService reporteService) {
        this.reporteService = reporteService;
    }

    @GetMapping("/reportes")
    public String index(@RequestParam(required = false) String periodo,
                         @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaInicio,
                         @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaFin,
                         @RequestParam(required = false) Long idEmpleado,
                         @RequestParam(required = false) Long idProducto,
                         Model model) {
        model.addAttribute("ventasPorPeriodo", reporteService.ventasPorPeriodo(periodo));
        model.addAttribute("gastosPorCategoria", reporteService.gastosPorCategoria());
        model.addAttribute("productosMasVendidos", reporteService.productosMasVendidos());
        model.addAttribute("balance", reporteService.balanceVentasGastos(periodo));
        model.addAttribute("periodo", periodo == null ? "mensual" : periodo);

        // ===== KPIs del tablero estrategico (respetan rango de fechas y empleado) =====
        var kpis = reporteService.computarKpis(fechaInicio, fechaFin, idEmpleado);
        model.addAttribute("ingresosTotales", kpis.get("ingresos"));
        model.addAttribute("gastosTotales", kpis.get("gastos"));
        model.addAttribute("utilidadNeta", kpis.get("utilidad"));
        model.addAttribute("margenGanancia", kpis.get("margen"));
        model.addAttribute("ticketPromedio", kpis.get("ticket"));

        // ===== Insights de negocio =====
        model.addAttribute("ventasPorEmpleado", reporteService.ventasPorEmpleado());
        model.addAttribute("productosSinRotacion", reporteService.productosSinRotacion());

        // Filtros multicriterio (rango de fechas, empleado, producto) sobre el detalle de ventas.
        model.addAttribute("ventasFiltradas",
                reporteService.ventasFiltradas(fechaInicio, fechaFin, idEmpleado, idProducto));
        model.addAttribute("empleadosFiltro", reporteService.listaEmpleadosParaFiltro());
        model.addAttribute("productosFiltro", reporteService.listaProductosParaFiltro());
        model.addAttribute("fechaInicio", fechaInicio);
        model.addAttribute("fechaFin", fechaFin);
        model.addAttribute("idEmpleado", idEmpleado);
        model.addAttribute("idProducto", idProducto);

        return "reportes/index";
    }

    @GetMapping("/reportes/ventas/pdf")
    public String ventasPdf(@RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaInicio,
                             @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaFin,
                             @RequestParam(required = false) Long idEmpleado,
                             @RequestParam(required = false) Long idProducto,
                             Model model) {
        model.addAttribute("ventas",
                reporteService.ventasFiltradas(fechaInicio, fechaFin, idEmpleado, idProducto));
        return "reportes/ventas_pdf";
    }

    @GetMapping("/reportes/gastos/pdf")
    public String gastosPdf(Model model) {
        model.addAttribute("gastos", reporteService.gastosDetallados());
        return "reportes/gastos_pdf";
    }

    @GetMapping("/reportes/excel")
    public ResponseEntity<byte[]> excel(@RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaInicio,
                                         @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaFin,
                                         @RequestParam(required = false) Long idEmpleado,
                                         @RequestParam(required = false) Long idProducto) {
        var ventas = reporteService.ventasFiltradas(fechaInicio, fechaFin, idEmpleado, idProducto);
        StringBuilder sb = new StringBuilder();
        sb.append("ID;Fecha;Empleado;Producto;Total\r\n");
        for (var v : ventas) {
            sb.append(v.get("id_venta")).append(';')
              .append(v.get("fecha")).append(';')
              .append(v.get("empleado")).append(';')
              .append(v.get("producto")).append(';')
              .append(v.get("total")).append("\r\n");
        }
        byte[] body = sb.toString().getBytes(StandardCharsets.UTF_8);
        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=ventas_postres_mariangel.csv")
                .contentType(MediaType.parseMediaType("text/csv"))
                .body(body);
    }
}
