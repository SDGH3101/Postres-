package com.example.demo.controlador;

import com.example.demo.config.UsuarioLogueado;
import com.example.demo.dto.RegistroForm;
import com.example.demo.service.AuthService;
import jakarta.servlet.http.HttpSession;
import jakarta.validation.Valid;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Autenticacion (equivale a AuthController de Laravel).
 */
@Controller
public class AuthController {

    private final AuthService authService;

    public AuthController(AuthService authService) {
        this.authService = authService;
    }

    @GetMapping("/")
    public String inicio(HttpSession session) {
        if (session.getAttribute("usuario") != null) {
            return "redirect:/dashboard";
        }
        return "redirect:/login";
    }

    @GetMapping("/login")
    public String showLogin(HttpSession session) {
        if (session.getAttribute("usuario") != null) {
            return "redirect:/dashboard";
        }
        return "auth/login";
    }

    @PostMapping("/login")
    public String login(@RequestParam String correo,
                        @RequestParam String pass,
                        HttpSession session,
                        Model model,
                        RedirectAttributes ra) {
        try {
            UsuarioLogueado u = authService.login(correo, pass);
            session.setAttribute("usuario", u);
            return "redirect:/dashboard";
        } catch (IllegalArgumentException e) {
            model.addAttribute("error", e.getMessage());
            model.addAttribute("correo", correo);
            return "auth/login";
        }
    }

    @GetMapping("/register")
    public String showRegister(Model model) {
        model.addAttribute("registroForm", new RegistroForm());
        return "auth/register";
    }

    @PostMapping("/register")
    public String register(@Valid @ModelAttribute("registroForm") RegistroForm form,
                           BindingResult result,
                           Model model,
                           RedirectAttributes ra) {
        // Validacion cruzada (no expresable con una sola anotacion de campo)
        if (form.getPass() != null && !form.getPass().equals(form.getPassConfirmation())) {
            result.rejectValue("passConfirmation", "match", "Las contraseñas no coinciden.");
        }
        if (result.hasErrors()) {
            return "auth/register";
        }
        try {
            authService.registrarCliente(form.getNombre(), form.getCorreo(),
                    form.getFechaNacimiento(), form.getPass());
            ra.addFlashAttribute("success", "¡Cuenta creada! Ya puedes ingresar.");
            return "redirect:/login";
        } catch (Exception e) {
            model.addAttribute("error", e.getMessage());
            return "auth/register";
        }
    }

    @PostMapping("/logout")
    public String logout(HttpSession session) {
        session.invalidate();
        return "redirect:/login";
    }
}
