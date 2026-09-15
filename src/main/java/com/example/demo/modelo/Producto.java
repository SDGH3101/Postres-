package com.example.demo.modelo;

import jakarta.persistence.*;
import jakarta.validation.constraints.*;
import org.springframework.format.annotation.DateTimeFormat;

import java.math.BigDecimal;
import java.time.LocalDate;

@Entity
@Table(name = "producto")
public class Producto {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_producto")
    private Long idProducto;

    @NotBlank(message = "La descripción es obligatoria.")
    @Size(min = 3, max = 200, message = "La descripción debe tener entre 3 y 200 caracteres.")
    @Column(name = "descripcion", length = 200)
    private String descripcion;

    @NotBlank(message = "El tipo es obligatorio.")
    @Column(name = "tipo", length = 10)
    private String tipo;

    @NotNull(message = "El precio es obligatorio.")
    @DecimalMin(value = "0.0", inclusive = true, message = "El precio no puede ser negativo.")
    @Digits(integer = 10, fraction = 2, message = "El precio tiene un formato numérico inválido.")
    @Column(name = "precio")
    private BigDecimal precio;

    @NotNull(message = "El stock es obligatorio.")
    @PositiveOrZero(message = "El stock no puede ser negativo.")
    @Column(name = "stock")
    private Integer stock;

    @DateTimeFormat(iso = DateTimeFormat.ISO.DATE)
    @Column(name = "fecha_caducidad")
    private LocalDate fechaCaducidad;

    @PositiveOrZero(message = "El stock mínimo no puede ser negativo.")
    @Column(name = "stock_minimo")
    private Integer stockMinimo;

    public Producto() {
    }

    public Long getIdProducto() {
        return idProducto;
    }

    public void setIdProducto(Long idProducto) {
        this.idProducto = idProducto;
    }

    public String getDescripcion() {
        return descripcion;
    }

    public void setDescripcion(String descripcion) {
        this.descripcion = descripcion;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public BigDecimal getPrecio() {
        return precio;
    }

    public void setPrecio(BigDecimal precio) {
        this.precio = precio;
    }

    public Integer getStock() {
        return stock;
    }

    public void setStock(Integer stock) {
        this.stock = stock;
    }

    public LocalDate getFechaCaducidad() {
        return fechaCaducidad;
    }

    public void setFechaCaducidad(LocalDate fechaCaducidad) {
        this.fechaCaducidad = fechaCaducidad;
    }

    public Integer getStockMinimo() {
        return stockMinimo;
    }

    public void setStockMinimo(Integer stockMinimo) {
        this.stockMinimo = stockMinimo;
    }

    /**
     * Calcula el estado de inventario con la misma precedencia que el modelo Laravel:
     * vencido > por_vencer > agotado > bajo > ok
     */
    public String getEstadoInventario() {
        LocalDate hoy = LocalDate.now();
        if (fechaCaducidad != null) {
            long dias = java.time.temporal.ChronoUnit.DAYS.between(hoy, fechaCaducidad);
            if (dias < 0) {
                return "vencido";
            }
            if (dias <= 3) {
                return "por_vencer";
            }
        }
        if (stock != null && stock <= 0) {
            return "agotado";
        }
        if (stock != null && stockMinimo != null && stock <= stockMinimo) {
            return "bajo";
        }
        return "ok";
    }
}
