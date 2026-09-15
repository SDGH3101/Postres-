package com.example.demo.service;

import com.example.demo.modelo.Empleado;
import com.example.demo.modelo.Usuario;
import com.example.demo.repository.EmpleadoRepository;
import com.example.demo.repository.UsuarioRepository;
import com.example.demo.repository.VentaRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

/**
 * Gestion de empleados (equivalente a EmpleadoController de Laravel - solo emprendedor).
 */
@Service
public class EmpleadoService {

    private final EmpleadoRepository empleadoRepository;
    private final UsuarioRepository usuarioRepository;
    private final VentaRepository ventaRepository;
    private final AuthService authService;

    public EmpleadoService(EmpleadoRepository empleadoRepository,
                           UsuarioRepository usuarioRepository,
                           VentaRepository ventaRepository,
                           AuthService authService) {
        this.empleadoRepository = empleadoRepository;
        this.usuarioRepository = usuarioRepository;
        this.ventaRepository = ventaRepository;
        this.authService = authService;
    }

    public List<Object[]> listar() {
        return empleadoRepository.listarEmpleados();
    }

    public Optional<Object[]> rendimiento(Long id) {
        return empleadoRepository.rendimientoEmpleado(id);
    }

    public Optional<Empleado> buscar(Long id) {
        return empleadoRepository.findByIdUsuario(id);
    }

    @Transactional
    public void crear(String nombre, String correo, LocalDate fechaNacimiento,
                      String pass, String cargo, BigDecimal salario, Integer horas) {
        if (usuarioRepository.existsByCorreo(correo.trim())) {
            throw new IllegalArgumentException("Ese correo ya está registrado.");
        }
        Usuario u = new Usuario();
        u.setNombre(nombre);
        u.setCorreo(correo.trim());
        u.setContrasena(authService.getEncoder().encode(pass));
        u.setEdad(AuthService.calcularEdad(fechaNacimiento));
        u.setFechaNacimiento(fechaNacimiento);
        u.setRegistro(LocalDate.now());
        u = usuarioRepository.save(u);

        Empleado e = new Empleado();
        e.setIdUsuario(u.getIdUsuario());
        e.setUsuario(u);
        e.setCargo(cargo);
        e.setSalario(salario);
        e.setHorasTrabajadas(horas);
        empleadoRepository.save(e);
    }

    @Transactional
    public void actualizar(Long id, String nombre, String correo, LocalDate fechaNacimiento,
                           String pass, String cargo, BigDecimal salario, Integer horas) {
        Empleado e = empleadoRepository.findByIdUsuario(id)
                .orElseThrow(() -> new IllegalArgumentException("Empleado no encontrado."));
        Usuario u = e.getUsuario();
        u.setNombre(nombre);
        u.setCorreo(correo.trim());
        u.setEdad(AuthService.calcularEdad(fechaNacimiento));
        u.setFechaNacimiento(fechaNacimiento);
        if (pass != null && !pass.isBlank()) {
            u.setContrasena(authService.getEncoder().encode(pass));
        }
        e.setCargo(cargo);
        e.setSalario(salario);
        e.setHorasTrabajadas(horas);
        usuarioRepository.save(u);
        empleadoRepository.save(e);
    }

    /**
     * Regla de negocio: si el empleado tiene ventas, se rechaza el borrado
     * (FK RESTRICT venta -> empleado, para conservar historial).
     */
    @Transactional
    public void eliminar(Long id) {
        Empleado e = empleadoRepository.findByIdUsuario(id)
                .orElseThrow(() -> new IllegalArgumentException("Empleado no encontrado."));
        long ventas = ventaRepository.count();
        // Contar ventas de este empleado
        Long ventasEmpleado = countVentasDe(id);
        if (ventasEmpleado > 0) {
            throw new IllegalArgumentException(
                "No se puede eliminar: el empleado tiene ventas registradas y se protege para conservar el historial.");
        }
        empleadoRepository.delete(e);
    }

    private Long countVentasDe(Long idEmpleado) {
        return ventaRepository.countVentasDeEmpleado(idEmpleado);
    }
}
