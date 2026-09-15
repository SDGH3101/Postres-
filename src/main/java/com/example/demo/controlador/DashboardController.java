package com.example.demo.controlador;

import com.example.demo.config.Sesion;
import com.example.demo.service.DashboardService;
import jakarta.servlet.http.HttpSession;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import java.math.BigDecimal;
import java.util.List;
import java.util.Map;

/**
 * Dashboard (equivale a DashboardController de Laravel).
 */
@Controller
public class DashboardController {

    private static final List<Object[]> LISTA_VACIA = List.of();

    private final DashboardService dashboardService;

    public DashboardController(DashboardService dashboardService) {
        this.dashboardService = dashboardService;
    }

    @GetMapping("/dashboard")
    public String index(HttpSession session, Model model) {
        // Defensa en profundidad: AuthInterceptor ya redirige, pero nunca confiar en un solo nivel.
        if (Sesion.obtener() == null) {
            return "redirect:/login";
        }

        String rol = Sesion.rol();

        // El cliente solo ve una bienvenida simple.
        if ("cliente".equals(rol)) {
            return "dashboard/cliente";
        }

        Map<String, Object> m = dashboardService.metricas();

        // Cada atributo requerido por la vista se inyecta explícitamente y jamás nulo.
        model.addAttribute("totalProductos", valor(m, "totalProductos", 0L));
        model.addAttribute("totalUsuarios", valor(m, "totalUsuarios", 0L));
        model.addAttribute("totalIngresos", valor(m, "totalIngresos", BigDecimal.ZERO));
        model.addAttribute("totalGastos", valor(m, "totalGastos", BigDecimal.ZERO));
        model.addAttribute("ventasRecientes", valor(m, "ventasRecientes", LISTA_VACIA));
        model.addAttribute("ventasMes", valor(m, "ventasMes", LISTA_VACIA));
        model.addAttribute("masVendidos", valor(m, "masVendidos", LISTA_VACIA));
        return "dashboard/index";
    }

    /** Devuelve el valor del mapa o el fallback si fuera null (nunca nulos en la vista). */
    private Object valor(Map<String, Object> datos, String clave, Object fallback) {
        return datos != null && datos.get(clave) != null ? datos.get(clave) : fallback;
    }
}