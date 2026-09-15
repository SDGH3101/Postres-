package com.example.demo.controlador;

import com.example.demo.config.Sesion;
import com.example.demo.config.UsuarioLogueado;
import com.example.demo.dto.UsuarioEditForm;
import com.example.demo.dto.UsuarioForm;
import com.example.demo.modelo.Usuario;
import com.example.demo.service.UsuarioService;
import jakarta.servlet.http.HttpSession;
import jakarta.validation.Valid;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Gestion de usuarios (equivale a UsuarioController de Laravel - solo emprendedor).
 */
@Controller
public class UsuarioController {

    private final UsuarioService usuarioService;

    public UsuarioController(UsuarioService usuarioService) {
        this.usuarioService = usuarioService;
    }

    @GetMapping("/usuarios")
    public String index(Model model) {
        model.addAttribute("usuarios", usuarioService.listarConRol());
        return "usuarios/index";
    }

    @GetMapping("/usuarios/nuevo")
    public String create(Model model) {
        model.addAttribute("usuario", new Usuario());
        model.addAttribute("usuarioForm", new UsuarioForm());
        return "usuarios/create";
    }

    @PostMapping("/usuarios")
    public String store(@Valid @ModelAttribute("usuarioForm") UsuarioForm form,
                        BindingResult result,
                        RedirectAttributes ra) {
        if (result.hasErrors()) {
            return "usuarios/create";
        }
        try {
            usuarioService.crear(form.getNombre(), form.getCorreo(), form.getFechaNacimiento(),
                    form.getPass(), form.getRol());
            ra.addFlashAttribute("success", "Usuario creado correctamente.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/usuarios";
    }

    @GetMapping("/usuarios/{id}")
    public String show(@PathVariable Long id, Model model) {
        model.addAttribute("usuario", usuarioService.buscarConRol(id)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado.")));
        model.addAttribute("usuarioId", id);
        return "usuarios/show";
    }

    @GetMapping("/usuarios/{id}/editar")
    public String edit(@PathVariable Long id, HttpSession session, Model model, RedirectAttributes ra) {
        if (!esMiCuenta(id, session)) {
            ra.addFlashAttribute("error", "Solo puedes editar tu propio perfil.");
            return "redirect:/usuarios";
        }
        Usuario u = usuarioService.buscar(id)
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado."));
        UsuarioEditForm form = new UsuarioEditForm();
        form.setNombre(u.getNombre());
        form.setFechaNacimiento(u.getFechaNacimiento());
        model.addAttribute("usuario", u);
        model.addAttribute("usuarioForm", form);
        return "usuarios/edit";
    }

    @PostMapping("/usuarios/{id}")
    public String update(@PathVariable Long id,
                         @Valid @ModelAttribute("usuarioForm") UsuarioEditForm form,
                         BindingResult result,
                         HttpSession session,
                         RedirectAttributes ra) {
        if (!esMiCuenta(id, session)) {
            ra.addFlashAttribute("error", "Solo puedes editar tu propio perfil.");
            return "redirect:/usuarios";
        }
        if (result.hasErrors()) {
            return "usuarios/edit";
        }
        try {
            usuarioService.actualizar(id, form.getNombre(), form.getFechaNacimiento(), form.getPass());
            ra.addFlashAttribute("success", "Usuario actualizado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/usuarios";
    }

    @PostMapping("/usuarios/{id}/eliminar")
    public String destroy(@PathVariable Long id, HttpSession session, RedirectAttributes ra) {
        if (!esMiCuenta(id, session)) {
            ra.addFlashAttribute("error", "Solo puedes eliminar tu propio perfil.");
            return "redirect:/usuarios";
        }
        try {
            Long enSesion = Sesion.idUsuario();
            usuarioService.eliminar(id, enSesion);
            if (id.equals(enSesion)) {
                if (Sesion.session() != null) {
                    Sesion.session().invalidate();
                }
                ra.addFlashAttribute("success", "Tu cuenta fue eliminada.");
                return "redirect:/login";
            }
            ra.addFlashAttribute("success", "Usuario eliminado.");
        } catch (Exception e) {
            ra.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/usuarios";
    }

    private boolean esMiCuenta(Long id, HttpSession session) {
        Object obj = session.getAttribute(Sesion.ATTR_USUARIO);
        return obj instanceof UsuarioLogueado u && u.getId().equals(id);
    }
}
