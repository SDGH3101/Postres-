package com.example.demo.config;

import org.springframework.web.context.request.RequestContextHolder;
import org.springframework.web.context.request.ServletRequestAttributes;

import jakarta.servlet.http.HttpSession;

/**
 * Utilidades de sesion (equivalente a session('usuario') de Laravel).
 * Guarda la informacion del usuario logueado bajo el atributo "usuario".
 */
public class Sesion {

    public static final String ATTR_USUARIO = "usuario";

    public static HttpSession session() {
        ServletRequestAttributes attrs = (ServletRequestAttributes) RequestContextHolder.getRequestAttributes();
        return attrs != null ? attrs.getRequest().getSession(false) : null;
    }

    public static UsuarioLogueado obtener() {
        HttpSession s = session();
        if (s == null) return null;
        Object obj = s.getAttribute(ATTR_USUARIO);
        if (obj instanceof UsuarioLogueado u) return u;
        return null;
    }

    public static boolean estaLogueado() {
        return obtener() != null;
    }

    public static String rol() {
        UsuarioLogueado u = obtener();
        return u != null ? u.getRol() : null;
    }

    public static Long idUsuario() {
        UsuarioLogueado u = obtener();
        return u != null ? u.getId() : null;
    }

    public static String nombre() {
        UsuarioLogueado u = obtener();
        return u != null ? u.getNombre() : null;
    }
}
