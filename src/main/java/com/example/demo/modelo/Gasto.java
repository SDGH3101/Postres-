package com.example.demo.modelo;

import jakarta.persistence.*;

import java.math.BigDecimal;
import java.time.LocalDate;

@Entity
@Table(name = "gasto")
public class Gasto {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_gasto")
    private Long idGasto;

    @Column(name = "descripcion", length = 200)
    private String descripcion;

    @Column(name = "categoria", length = 80)
    private String categoria;

    @Column(name = "fecha")
    private LocalDate fecha;

    @Column(name = "monto")
    private BigDecimal monto;

    @Column(name = "presupuesto")
    private BigDecimal presupuesto;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_emprendedor")
    private Emprendedor emprendedor;

    public Gasto() {
    }

    public Long getIdGasto() {
        return idGasto;
    }

    public void setIdGasto(Long idGasto) {
        this.idGasto = idGasto;
    }

    public String getDescripcion() {
        return descripcion;
    }

    public void setDescripcion(String descripcion) {
        this.descripcion = descripcion;
    }

    public String getCategoria() {
        return categoria;
    }

    public void setCategoria(String categoria) {
        this.categoria = categoria;
    }

    public LocalDate getFecha() {
        return fecha;
    }

    public void setFecha(LocalDate fecha) {
        this.fecha = fecha;
    }

    public BigDecimal getMonto() {
        return monto;
    }

    public void setMonto(BigDecimal monto) {
        this.monto = monto;
    }

    public BigDecimal getPresupuesto() {
        return presupuesto;
    }

    public void setPresupuesto(BigDecimal presupuesto) {
        this.presupuesto = presupuesto;
    }

    public Emprendedor getEmprendedor() {
        return emprendedor;
    }

    public void setEmprendedor(Emprendedor emprendedor) {
        this.emprendedor = emprendedor;
    }
}
