package com.example.demo.controlador;

import com.example.demo.modelo.Empleado;
import com.example.demo.service.EmpleadoService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.math.BigDecimal;
import java.time.LocalDate;

/**
 * Gestion de empleados (equivale a EmpleadoController de Laravel - solo emprendedor).
 */
@Controller
public class EmpleadoController {

    private final EmpleadoService empleadoService;

    public EmpleadoController(EmpleadoService empleadoService) {
        this.empleadoService = empleadoService;
    }

    @GetMapping("/empleados")
    public String index(Model model) {
        model.addAttribute("empleados", empleadoService.listar());
        return "empleados/index";
    }

    @GetMapping("/empleados/nuevo")
    public String create(Model model) {
        model.addAttribute("empleado", new Empleado());
        return "empleados/create";
    }

    @PostMapping("/empleados")
    public String store(@RequestParam String nombre,
                        @RequestParam String correo,
                        @RequestParam String fecha_nacimiento,
                        @RequestParam String pass,
                        @RequestParam String cargo,
                        @RequestParam BigDecimal salario,
                        @RequestParam Integer horas_trabajadas,
                        RedirectAttributes ra) {
        try {
            empleadoService.crear(nombre, correo, LocalDate.parse(fecha_nacimiento), pass,
                    cargo, salario, horas_trabajadas);
            ra.addFlashAttribute("success", "Empleado creado correctamente.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/empleados";
    }

    @GetMapping("/empleados/{id}")
    public String show(@PathVariable Long id, Model model) {
        model.addAttribute("rendimiento", empleadoService.rendimiento(id)
                .orElseThrow(() -> new RuntimeException("Empleado no encontrado.")));
        return "empleados/show";
    }

    @GetMapping("/empleados/{id}/editar")
    public String edit(@PathVariable Long id, Model model) {
        Empleado e = empleadoService.buscar(id)
                .orElseThrow(() -> new RuntimeException("Empleado no encontrado."));
        model.addAttribute("empleado", e);
        return "empleados/edit";
    }

    @PostMapping("/empleados/{id}")
    public String update(@PathVariable Long id,
                         @RequestParam String nombre,
                         @RequestParam String correo,
                         @RequestParam String fecha_nacimiento,
                         @RequestParam(required = false) String pass,
                         @RequestParam String cargo,
                         @RequestParam BigDecimal salario,
                         @RequestParam Integer horas_trabajadas,
                         RedirectAttributes ra) {
        try {
            empleadoService.actualizar(id, nombre, correo, LocalDate.parse(fecha_nacimiento), pass,
                    cargo, salario, horas_trabajadas);
            ra.addFlashAttribute("success", "Empleado actualizado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/empleados";
    }

    @PostMapping("/empleados/{id}/eliminar")
    public String destroy(@PathVariable Long id, RedirectAttributes ra) {
        try {
            empleadoService.eliminar(id);
            ra.addFlashAttribute("success", "Empleado eliminado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/empleados";
    }
}
