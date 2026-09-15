package com.example.demo.config;

import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ModelAttribute;

/**
 * Expone en el modelo (para todas las vistas) la informacion del usuario
 * logueado, de forma equivalente a session('usuario.x') en las vistas Blade.
 */
@ControllerAdvice
public class GlobalModelAdvice {

    @ModelAttribute("sesionUsuario")
    public UsuarioLogueado sesionUsuario() {
        return Sesion.obtener();
    }

    @ModelAttribute("rolSesion")
    public String rolSesion() {
        return Sesion.rol();
    }

    @ModelAttribute("sesionId")
    public Long sesionId() {
        return Sesion.idUsuario();
    }
}
