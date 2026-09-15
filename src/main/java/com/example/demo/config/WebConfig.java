package com.example.demo.config;

import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.InterceptorRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

/**
 * Registra los interceptores de sesion y roles (equivale a los middleware
 * auth.session y role:... de Laravel en routes/web.php).
 */
@Configuration
public class WebConfig implements WebMvcConfigurer {

    @Override
    public void addInterceptors(InterceptorRegistry registry) {

        // Protege todo excepto autenticacion y archivos estaticos
        registry.addInterceptor(new AuthInterceptor())
                .addPathPatterns("/**")
                .excludePathPatterns(
                        "/login", "/register",
                        "/css/**", "/js/**", "/images/**", "/img/**",
                        "/favicon.ico", "/error"
                );

        // Solo emprendedor y empleado
        registry.addInterceptor(new RoleInterceptor("emprendedor", "empleado"))
                .addPathPatterns("/productos/**", "/compras/**", "/ventas/**");

        // Solo emprendedor
        registry.addInterceptor(new RoleInterceptor("emprendedor"))
                .addPathPatterns(
                        "/usuarios/**", "/empleados/**",
                        "/reportes/**", "/presupuestos/**", "/flujo-caja/**"
                );
    }
}
