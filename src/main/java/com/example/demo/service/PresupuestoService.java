package com.example.demo.service;

import com.example.demo.modelo.Presupuesto;
import com.example.demo.repository.ComprasMenoresRepository;
import com.example.demo.repository.EmprendedorRepository;
import com.example.demo.repository.GastoRepository;
import com.example.demo.repository.PresupuestoRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDate;
import java.time.temporal.ChronoUnit;
import java.util.List;

/**
 * Presupuestos (equivale a PresupuestoController de Laravel - solo emprendedor).
 * Reproduce la logica del modelo Laravel Presupuesto (rangoActual, gastoReal, porcentajeUsado).
 */
@Service
public class PresupuestoService {

    private final PresupuestoRepository presupuestoRepository;
    private final EmprendedorRepository emprendedorRepository;
    private final GastoRepository gastoRepository;
    private final ComprasMenoresRepository comprasMenoresRepository;

    public PresupuestoService(PresupuestoRepository presupuestoRepository,
                              EmprendedorRepository emprendedorRepository,
                              GastoRepository gastoRepository,
                              ComprasMenoresRepository comprasMenoresRepository) {
        this.presupuestoRepository = presupuestoRepository;
        this.emprendedorRepository = emprendedorRepository;
        this.gastoRepository = gastoRepository;
        this.comprasMenoresRepository = comprasMenoresRepository;
    }

    public List<Presupuesto> listarActivos(Long emprendedorId) {
        return presupuestoRepository.findByEmprendedor_IdUsuarioAndActivoTrueOrderByTipoAsc(emprendedorId);
    }

    @Transactional
    public void crear(Long emprendedorId, String tipo, BigDecimal monto, LocalDate fechaInicio) {
        Presupuesto p = new Presupuesto();
        p.setEmprendedor(emprendedorRepository.findById(emprendedorId).orElseThrow());
        p.setTipo(tipo);
        p.setMonto(monto);
        p.setFechaInicio(fechaInicio);
        p.setActivo(true);
        presupuestoRepository.save(p);
    }

    @Transactional
    public void eliminar(Long id, Long emprendedorId) {
        presupuestoRepository.deleteByIdPresupuestoAndEmprendedor_IdUsuario(id, emprendedorId);
    }

    // ----- Metodos del modelo Laravel -----

    /**
     * Rango [inicio, fin] del periodo vigente mas cercano a hoy.
     */
    public LocalDate[] rangoActual(Presupuesto presupuesto) {
        LocalDate inicio = presupuesto.getFechaInicio();
        LocalDate hoy = LocalDate.now();

        ChronoUnit unidad = switch (presupuesto.getTipo()) {
            case "diario" -> ChronoUnit.DAYS;
            case "semanal" -> ChronoUnit.WEEKS;
            case "mensual" -> ChronoUnit.MONTHS;
            case "anual" -> ChronoUnit.YEARS;
            default -> ChronoUnit.MONTHS;
        };

        LocalDate cursor = inicio;
        while (cursor.plus(1, unidad).isBefore(hoy) || cursor.plus(1, unidad).isEqual(hoy)) {
            cursor = cursor.plus(1, unidad);
        }
        LocalDate fin = cursor.plus(1, unidad).minusDays(1);
        return new LocalDate[]{cursor, fin};
    }

    /**
     * Suma de gastos + compras menores del emprendedor dentro del rango actual.
     */
    public BigDecimal gastoReal(Presupuesto presupuesto) {
        LocalDate[] rango = rangoActual(presupuesto);
        Long empId = presupuesto.getEmprendedor().getIdUsuario();
        BigDecimal gastos = gastoRepository.sumMontoEntreDeEmprendedor(empId, rango[0], rango[1]);
        BigDecimal compras = comprasMenoresRepository.sumMontoEntreDeEmprendedor(empId, rango[0], rango[1]);
        return gastos.add(compras);
    }

    public BigDecimal porcentajeUsado(Presupuesto presupuesto) {
        if (presupuesto.getMonto().signum() <= 0) {
            return BigDecimal.ZERO;
        }
        BigDecimal pct = gastoReal(presupuesto)
                .multiply(BigDecimal.valueOf(100))
                .divide(presupuesto.getMonto(), 1, RoundingMode.HALF_UP);
        return pct;
    }
}
