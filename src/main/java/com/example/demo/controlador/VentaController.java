package com.example.demo.controlador;

import com.example.demo.dto.ItemVentaForm;
import com.example.demo.dto.VentaForm;
import com.example.demo.service.VentaService;
import jakarta.validation.Valid;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.ArrayList;
import java.util.List;

/**
 * Ventas con carrito multi-producto (equivale a VentaController de Laravel).
 * El formulario enlaza items[i].idProducto / items[i].cantidad con notacion
 * indexada estandar de Spring MVC, validados en cascada via VentaForm (@Valid).
 */
@Controller
public class VentaController {

    private final VentaService ventaService;

    public VentaController(VentaService ventaService) {
        this.ventaService = ventaService;
    }

    @GetMapping("/ventas")
    public String index(Model model) {
        model.addAttribute("ventas", ventaService.listarVentas());
        model.addAttribute("productos", ventaService.productosVenta());
        model.addAttribute("empleados", ventaService.empleados());
        model.addAttribute("ventaForm", new VentaForm());
        return "compras/ventas";
    }

    @GetMapping("/ventas/nueva")
    public String create(Model model) {
        return index(model);
    }

    @PostMapping("/ventas")
    public String store(@Valid @ModelAttribute("ventaForm") VentaForm form,
                        BindingResult result,
                        Model model,
                        RedirectAttributes ra) {
        if (result.hasErrors()) {
            model.addAttribute("ventas", ventaService.listarVentas());
            model.addAttribute("productos", ventaService.productosVenta());
            model.addAttribute("empleados", ventaService.empleados());
            return "compras/ventas";
        }
        try {
            List<VentaService.ItemVenta> items = new ArrayList<>();
            for (ItemVentaForm it : form.getItems()) {
                items.add(new VentaService.ItemVenta(it.getIdProducto(), it.getCantidad()));
            }
            ventaService.registrarVenta(form.getIdEmpleado(), form.getFecha(), form.getMedioPago(), items);
            ra.addFlashAttribute("success", "Venta registrada. Stock descontado automáticamente.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/ventas";
    }

    @PostMapping("/ventas/{id}/eliminar")
    public String destroy(@PathVariable Long id, RedirectAttributes ra) {
        try {
            ventaService.eliminarVenta(id);
            ra.addFlashAttribute("success", "Venta eliminada.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/ventas";
    }
}
