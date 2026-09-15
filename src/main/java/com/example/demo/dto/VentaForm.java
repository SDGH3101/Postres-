package com.example.demo.dto;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotEmpty;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.PastOrPresent;
import org.springframework.format.annotation.DateTimeFormat;

import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

/**
 * Formulario de registro de venta con carrito multi-producto.
 * El campo "items" se enlaza con notacion indexada de Spring
 * (items[0].idProducto, items[0].cantidad, items[1]...), y cada
 * elemento se valida en cascada gracias a @Valid.
 */
public class VentaForm {

    @NotNull(message = "Selecciona el empleado responsable.")
    private Long idEmpleado;

    @NotNull(message = "La fecha es obligatoria.")
    @PastOrPresent(message = "La fecha no puede ser futura")
    @DateTimeFormat(iso = DateTimeFormat.ISO.DATE)
    private LocalDate fecha;

    @NotBlank(message = "Selecciona el medio de pago.")
    private String medioPago;

    @NotEmpty(message = "Agrega al menos un producto al carrito.")
    @Valid
    private List<ItemVentaForm> items = new ArrayList<>();

    public Long getIdEmpleado() { return idEmpleado; }
    public void setIdEmpleado(Long idEmpleado) { this.idEmpleado = idEmpleado; }

    public LocalDate getFecha() { return fecha; }
    public void setFecha(LocalDate fecha) { this.fecha = fecha; }

    public String getMedioPago() { return medioPago; }
    public void setMedioPago(String medioPago) { this.medioPago = medioPago; }

    public List<ItemVentaForm> getItems() { return items; }
    public void setItems(List<ItemVentaForm> items) { this.items = items; }
}
