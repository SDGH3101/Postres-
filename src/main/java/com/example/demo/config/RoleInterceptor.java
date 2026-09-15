package com.example.demo.config;

import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import org.springframework.web.servlet.HandlerInterceptor;

import java.util.List;

/**
 * Equivalente al middleware CheckRole de Laravel (role:emprendedor,empleado).
 * Verifica que el rol del usuario en sesion este dentro de los roles permitidos.
 */
public class RoleInterceptor implements HandlerInterceptor {

    private final List<String> rolesPermitidos;

    public RoleInterceptor(String... roles) {
        this.rolesPermitidos = List.of(roles);
    }

    @Override
    public boolean preHandle(HttpServletRequest request, HttpServletResponse response, Object handler)
            throws Exception {
        if (request.getMethod().equalsIgnoreCase("OPTIONS")) {
            return true;
        }
        String rol = Sesion.rol();
        if (rol != null && rolesPermitidos.contains(rol)) {
            return true;
        }
        // Rechaza con redireccion al dashboard (igual que Laravel redirige con mensaje de error)
        response.sendRedirect(request.getContextPath() + "/dashboard");
        return false;
    }
}
