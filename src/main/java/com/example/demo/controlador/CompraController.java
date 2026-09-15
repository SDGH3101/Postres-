package com.example.demo.controlador;

import com.example.demo.modelo.ComprasMenores;
import com.example.demo.modelo.Gasto;
import com.example.demo.service.CompraService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.math.BigDecimal;
import java.time.LocalDate;

/**
 * Gastos / Compras menores (equivale a CompraController de Laravel).
 */
@Controller
public class CompraController {

    private final CompraService compraService;

    public CompraController(CompraService compraService) {
        this.compraService = compraService;
    }

    @GetMapping("/compras")
    public String index(Model model) {
        model.addAttribute("gastos", compraService.listarGastos());
        model.addAttribute("compras", compraService.listarCompras());
        model.addAttribute("categorias", compraService.listarCategorias());
        model.addAttribute("totalGastos", compraService.totalGastos());
        return "compras/index";
    }

    @GetMapping("/compras/nueva")
    public String create(Model model) {
        model.addAttribute("categorias", compraService.listarCategorias());
        // Objetos de respaldo instanciados para que la vista nunca falle por atributos nulos
        model.addAttribute("gasto", new Gasto());
        model.addAttribute("compra", new ComprasMenores());
        return "compras/create";
    }

    @PostMapping("/compras")
    public String store(@RequestParam String tipo,
                        @RequestParam String descripcion,
                        @RequestParam String fecha,
                        @RequestParam BigDecimal monto,
                        @RequestParam(required = false) String categoria,
                        @RequestParam(required = false) Long id_categoria,
                        RedirectAttributes ra) {
        try {
            compraService.registrar(tipo, descripcion, LocalDate.parse(fecha), monto, categoria, id_categoria);
            ra.addFlashAttribute("success", "Registro guardado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/compras";
    }

    @GetMapping("/compras/{id}/editar")
    public String edit(@PathVariable Long id, Model model) {
        Gasto g = compraService.buscarGasto(id)
                .orElseThrow(() -> new RuntimeException("Gasto no encontrado."));
        model.addAttribute("gasto", g);
        return "compras/edit";
    }

    @PostMapping("/compras/{id}")
    public String update(@PathVariable Long id,
                         @RequestParam String descripcion,
                         @RequestParam String categoria,
                         @RequestParam String fecha,
                         @RequestParam BigDecimal monto,
                         RedirectAttributes ra) {
        try {
            compraService.actualizarGasto(id, descripcion, categoria, LocalDate.parse(fecha), monto);
            ra.addFlashAttribute("success", "Gasto actualizado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/compras";
    }

    @PostMapping("/compras/{id}/eliminar")
    public String destroy(@PathVariable Long id, RedirectAttributes ra) {
        try {
            compraService.eliminarGasto(id);
            ra.addFlashAttribute("success", "Gasto eliminado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/compras";
    }
}
