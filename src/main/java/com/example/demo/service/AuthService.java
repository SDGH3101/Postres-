package com.example.demo.service;

import com.example.demo.config.UsuarioLogueado;
import com.example.demo.modelo.Cliente;
import com.example.demo.modelo.Empleado;
import com.example.demo.modelo.Emprendedor;
import com.example.demo.modelo.Usuario;
import com.example.demo.repository.ClienteRepository;
import com.example.demo.repository.EmpleadoRepository;
import com.example.demo.repository.EmprendedorRepository;
import com.example.demo.repository.UsuarioRepository;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.time.Period;
import java.util.Optional;

/**
 * Autenticacion y registro de cuentas (equivale a AuthController de Laravel).
 */
@Service
public class AuthService {

    private final UsuarioRepository usuarioRepository;
    private final EmprendedorRepository emprendedorRepository;
    private final EmpleadoRepository empleadoRepository;
    private final ClienteRepository clienteRepository;
    private final BCryptPasswordEncoder encoder;

    public AuthService(UsuarioRepository usuarioRepository,
                       EmprendedorRepository emprendedorRepository,
                       EmpleadoRepository empleadoRepository,
                       ClienteRepository clienteRepository) {
        this.usuarioRepository = usuarioRepository;
        this.emprendedorRepository = emprendedorRepository;
        this.empleadoRepository = empleadoRepository;
        this.clienteRepository = clienteRepository;
        this.encoder = new BCryptPasswordEncoder();
    }

    public BCryptPasswordEncoder getEncoder() {
        return encoder;
    }

    /**
     * Verifica credenciales y devuelve la informacion de sesion o lanza error.
     */
    public UsuarioLogueado login(String correo, String pass) {
        Optional<Usuario> opt = usuarioRepository.findByCorreo(correo.trim());
        if (opt.isEmpty() || !encoder.matches(pass, opt.get().getContrasena())) {
            throw new IllegalArgumentException("Correo o contraseña incorrectos.");
        }
        Usuario u = opt.get();
        String rol = detectarRol(u.getIdUsuario());
        return new UsuarioLogueado(u.getIdUsuario(), u.getNombre(), u.getCorreo(), rol);
    }

    private String detectarRol(Long idUsuario) {
        if (emprendedorRepository.existsByIdUsuario(idUsuario)) return "emprendedor";
        if (empleadoRepository.existsByIdUsuario(idUsuario)) return "empleado";
        if (clienteRepository.existsByIdUsuario(idUsuario)) return "cliente";
        return "cliente";
    }

    /**
     * Registra un cliente (equivale al register de Laravel).
     */
    @Transactional
    public void registrarCliente(String nombre, String correo, LocalDate fechaNacimiento, String pass) {
        if (usuarioRepository.existsByCorreo(correo.trim())) {
            throw new IllegalArgumentException("Ese correo ya está registrado.");
        }
        Usuario u = new Usuario();
        u.setNombre(nombre);
        u.setCorreo(correo.trim());
        u.setContrasena(encoder.encode(pass));
        u.setEdad(calcularEdad(fechaNacimiento));
        u.setFechaNacimiento(fechaNacimiento);
        u.setRegistro(LocalDate.now());
        u = usuarioRepository.save(u);

        Cliente c = new Cliente();
        c.setIdUsuario(u.getIdUsuario());
        c.setUsuario(u);
        clienteRepository.save(c);
    }

    public static int calcularEdad(LocalDate fechaNacimiento) {
        return Period.between(fechaNacimiento, LocalDate.now()).getYears();
    }
}
