package com.example.demo.service;

import com.example.demo.modelo.Cliente;
import com.example.demo.modelo.Empleado;
import com.example.demo.modelo.Emprendedor;
import com.example.demo.modelo.Usuario;
import com.example.demo.repository.ClienteRepository;
import com.example.demo.repository.EmpleadoRepository;
import com.example.demo.repository.EmprendedorRepository;
import com.example.demo.repository.UsuarioRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

/**
 * Gestion de usuarios (equivalente a UsuarioController de Laravel - solo emprendedor).
 */
@Service
public class UsuarioService {

    private final UsuarioRepository usuarioRepository;
    private final EmprendedorRepository emprendedorRepository;
    private final EmpleadoRepository empleadoRepository;
    private final ClienteRepository clienteRepository;
    private final AuthService authService;

    public UsuarioService(UsuarioRepository usuarioRepository,
                          EmprendedorRepository emprendedorRepository,
                          EmpleadoRepository empleadoRepository,
                          ClienteRepository clienteRepository,
                          AuthService authService) {
        this.usuarioRepository = usuarioRepository;
        this.emprendedorRepository = emprendedorRepository;
        this.empleadoRepository = empleadoRepository;
        this.clienteRepository = clienteRepository;
        this.authService = authService;
    }

    public List<Object[]> listarConRol() {
        return usuarioRepository.listarUsuariosConRol();
    }

    public Optional<Object[]> buscarConRol(Long id) {
        return usuarioRepository.buscarUsuarioConRol(id);
    }

    public Optional<Usuario> buscar(Long id) {
        return usuarioRepository.findById(id);
    }

    @Transactional
    public void crear(String nombre, String correo, LocalDate fechaNacimiento, String pass, String rol) {
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

        switch (rol) {
            case "emprendedor" -> {
                Emprendedor e = new Emprendedor();
                e.setIdUsuario(u.getIdUsuario());
                e.setUsuario(u);
                e.setDescripcion("Postres Mariangel");
                emprendedorRepository.save(e);
            }
            case "empleado" -> {
                Empleado em = new Empleado();
                em.setIdUsuario(u.getIdUsuario());
                em.setUsuario(u);
                em.setCargo("Empleado");
                em.setSalario(BigDecimal.ZERO);
                em.setHorasTrabajadas(0);
                empleadoRepository.save(em);
            }
            default -> {
                Cliente c = new Cliente();
                c.setIdUsuario(u.getIdUsuario());
                c.setUsuario(u);
                clienteRepository.save(c);
            }
        }
    }

    @Transactional
    public void actualizar(Long id, String nombre, LocalDate fechaNacimiento, String pass) {
        Usuario u = usuarioRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Usuario no encontrado."));
        u.setNombre(nombre);
        u.setFechaNacimiento(fechaNacimiento);
        u.setEdad(AuthService.calcularEdad(fechaNacimiento));
        if (pass != null && !pass.isBlank()) {
            u.setContrasena(authService.getEncoder().encode(pass));
        }
        usuarioRepository.save(u);
    }

    /**
     * Regla de negocio: solo el propio usuario puede eliminar su cuenta.
     */
    @Transactional
    public void eliminar(Long id, Long usuarioEnSesion) {
        if (!id.equals(usuarioEnSesion)) {
            throw new IllegalArgumentException(
                "Solo el propio usuario puede eliminar su cuenta. El administrador no tiene permiso para borrar cuentas ajenas.");
        }
        usuarioRepository.deleteById(id);
    }

    public boolean esMiCuenta(Long id, Long usuarioEnSesion) {
        return id.equals(usuarioEnSesion);
    }
}
