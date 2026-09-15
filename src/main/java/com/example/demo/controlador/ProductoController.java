package com.example.demo.controlador;

import com.example.demo.modelo.Producto;
import com.example.demo.service.ProductoService;
import jakarta.validation.Valid;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Productos / Inventario y catalogo (equivale a ProductoController de Laravel).
 *
 * Validacion server-side: los metodos store/update reciben el Producto ya
 * enlazado por Spring (th:object en la vista) y validado con Bean Validation
 * (ver anotaciones en el modelo Producto). Si BindingResult trae errores,
 * se re-muestra el formulario con los mensajes, sin tocar la base de datos.
 */
@Controller
public class ProductoController {

    private final ProductoService productoService;

    public ProductoController(ProductoService productoService) {
        this.productoService = productoService;
    }

    @GetMapping("/productos")
    public String index(Model model) {
        model.addAttribute("productos", productoService.listar());
        return "productos/index";
    }

    @GetMapping("/catalogo")
    public String catalogo(Model model) {
        model.addAttribute("productos", productoService.catalogo());
        return "catalogo/index";
    }

    @GetMapping("/productos/nuevo")
    public String create(Model model) {
        model.addAttribute("producto", new Producto());
        return "productos/create";
    }

    @PostMapping("/productos")
    public String store(@Valid @ModelAttribute("producto") Producto producto,
                        BindingResult result,
                        RedirectAttributes ra) {
        if (result.hasErrors()) {
            return "productos/create";
        }
        try {
            productoService.crear(producto.getDescripcion(), producto.getTipo(), producto.getPrecio(),
                    producto.getStock(), producto.getStockMinimo(), producto.getFechaCaducidad());
            ra.addFlashAttribute("success", "Producto registrado correctamente.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/productos";
    }

    @GetMapping("/productos/{id}")
    public String show(@PathVariable Long id, Model model) {
        Producto p = productoService.buscar(id)
                .orElseThrow(() -> new RuntimeException("Producto no encontrado."));
        model.addAttribute("producto", p);
        return "productos/show";
    }

    @GetMapping("/productos/{id}/editar")
    public String edit(@PathVariable Long id, Model model) {
        Producto p = productoService.buscar(id)
                .orElseThrow(() -> new RuntimeException("Producto no encontrado."));
        model.addAttribute("producto", p);
        return "productos/edit";
    }

    @PostMapping("/productos/{id}")
    public String update(@PathVariable Long id,
                         @Valid @ModelAttribute("producto") Producto producto,
                         BindingResult result,
                         RedirectAttributes ra) {
        if (result.hasErrors()) {
            producto.setIdProducto(id);
            return "productos/edit";
        }
        try {
            productoService.actualizar(id, producto.getDescripcion(), producto.getTipo(), producto.getPrecio(),
                    producto.getStock(), producto.getStockMinimo(), producto.getFechaCaducidad());
            ra.addFlashAttribute("success", "Producto actualizado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/productos";
    }

    @PostMapping("/productos/{id}/eliminar")
    public String destroy(@PathVariable Long id, RedirectAttributes ra) {
        try {
            productoService.eliminar(id);
            ra.addFlashAttribute("success", "Producto eliminado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/productos";
    }
}
