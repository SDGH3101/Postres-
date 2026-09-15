package com.example.demo.controlador;

import com.example.demo.service.FlujoCajaService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

import java.util.Arrays;
import java.util.List;

/**
 * Flujo de caja (equivale a FlujoCajaController de Laravel - solo emprendedor).
 */
@Controller
public class FlujoCajaController {

    private final FlujoCajaService flujoCajaService;

    public FlujoCajaController(FlujoCajaService flujoCajaService) {
        this.flujoCajaService = flujoCajaService;
    }

    @GetMapping("/flujo-caja")
    public String index(@RequestParam(required = false) String periodo, Model model) {
        if (!flujoCajaService.esPeriodoValido(periodo)) {
            periodo = "mensual";
        }
        List<String> periodos = Arrays.asList("diario", "semanal", "mensual", "anual");
        model.addAttribute("periodos", periodos);
        model.addAttribute("periodo", periodo);
        model.addAttribute("movimientos", flujoCajaService.flujoPorPeriodo(periodo));
        model.addAttribute("saldo", flujoCajaService.saldoRealCaja());
        return "flujo_caja/index";
    }
}
