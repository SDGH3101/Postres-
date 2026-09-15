package com.example.demo.controlador;

import com.example.demo.config.Sesion;
import com.example.demo.modelo.Presupuesto;
import com.example.demo.service.PresupuestoService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

/**
 * Presupuestos (equivale a PresupuestoController de Laravel - solo emprendedor).
 */
@Controller
public class PresupuestoController {

    private final PresupuestoService presupuestoService;

    public PresupuestoController(PresupuestoService presupuestoService) {
        this.presupuestoService = presupuestoService;
    }

    @GetMapping("/presupuestos")
    public String index(Model model) {
        Long empId = Sesion.idUsuario();
        List<Presupuesto> presupuestos = presupuestoService.listarActivos(empId);

        List<PresupuestoView> vista = new ArrayList<>();
        for (Presupuesto p : presupuestos) {
            LocalDate[] rango = presupuestoService.rangoActual(p);
            PresupuestoView v = new PresupuestoView();
            v.setPresupuesto(p);
            v.setGastoReal(presupuestoService.gastoReal(p));
            v.setPorcentaje(presupuestoService.porcentajeUsado(p));
            v.setRangoInicio(rango[0].toString());
            v.setRangoFin(rango[1].toString());
            vista.add(v);
        }
        model.addAttribute("presupuestos", vista);
        return "presupuestos/index";
    }

    @PostMapping("/presupuestos")
    public String store(@RequestParam String tipo,
                        @RequestParam BigDecimal monto,
                        @RequestParam String fecha_inicio,
                        RedirectAttributes ra) {
        try {
            presupuestoService.crear(Sesion.idUsuario(), tipo, monto, LocalDate.parse(fecha_inicio));
            ra.addFlashAttribute("success", "Presupuesto creado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/presupuestos";
    }

    @PostMapping("/presupuestos/{id}/eliminar")
    public String destroy(@PathVariable Long id, RedirectAttributes ra) {
        try {
            presupuestoService.eliminar(id, Sesion.idUsuario());
            ra.addFlashAttribute("success", "Presupuesto eliminado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/presupuestos";
    }

    public static class PresupuestoView {
        private Presupuesto presupuesto;
        private BigDecimal gastoReal;
        private BigDecimal porcentaje;
        private String rangoInicio;
        private String rangoFin;

        public Presupuesto getPresupuesto() { return presupuesto; }
        public void setPresupuesto(Presupuesto presupuesto) { this.presupuesto = presupuesto; }
        public BigDecimal getGastoReal() { return gastoReal; }
        public void setGastoReal(BigDecimal gastoReal) { this.gastoReal = gastoReal; }
        public BigDecimal getPorcentaje() { return porcentaje; }
        public void setPorcentaje(BigDecimal porcentaje) { this.porcentaje = porcentaje; }
        public String getRangoInicio() { return rangoInicio; }
        public void setRangoInicio(String rangoInicio) { this.rangoInicio = rangoInicio; }
        public String getRangoFin() { return rangoFin; }
        public void setRangoFin(String rangoFin) { this.rangoFin = rangoFin; }
    }
}
