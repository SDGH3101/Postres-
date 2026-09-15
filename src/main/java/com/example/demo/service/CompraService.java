package com.example.demo.service;

import com.example.demo.modelo.Categoria;
import com.example.demo.modelo.ComprasMenores;
import com.example.demo.modelo.Emprendedor;
import com.example.demo.modelo.Gasto;
import com.example.demo.repository.CategoriaRepository;
import com.example.demo.repository.ComprasMenoresRepository;
import com.example.demo.repository.EmprendedorRepository;
import com.example.demo.repository.GastoRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

/**
 * Gestion de gastos y compras menores (equivale a CompraController de Laravel).
 */
@Service
public class CompraService {

    private final GastoRepository gastoRepository;
    private final ComprasMenoresRepository comprasMenoresRepository;
    private final CategoriaRepository categoriaRepository;
    private final EmprendedorRepository emprendedorRepository;

    public CompraService(GastoRepository gastoRepository,
                         ComprasMenoresRepository comprasMenoresRepository,
                         CategoriaRepository categoriaRepository,
                         EmprendedorRepository emprendedorRepository) {
        this.gastoRepository = gastoRepository;
        this.comprasMenoresRepository = comprasMenoresRepository;
        this.categoriaRepository = categoriaRepository;
        this.emprendedorRepository = emprendedorRepository;
    }

    public List<Object[]> listarGastos() {
        return gastoRepository.listarGastos();
    }

    public List<Object[]> listarCompras() {
        return comprasMenoresRepository.listarCompras();
    }

    public List<Categoria> listarCategorias() {
        return categoriaRepository.findAll();
    }

    public BigDecimal totalGastos() {
        return gastoRepository.sumTotalMonto().add(comprasMenoresRepository.sumTotalMonto());
    }

    @Transactional
    public void registrar(String tipo, String descripcion, LocalDate fecha, BigDecimal monto,
                          String categoria, Long idCategoria) {
        // Se toma el primer emprendedor (igual que la logica original de Laravel)
        Long idEmprendedor = emprendedorRepository.findAllIds().stream()
                .findFirst()
                .orElseThrow(() -> new IllegalArgumentException("No hay un emprendedor registrado."));
        Emprendedor emprendedor = emprendedorRepository.findById(idEmprendedor).orElseThrow();

        if ("gasto".equals(tipo)) {
            Gasto g = new Gasto();
            g.setDescripcion(descripcion);
            g.setCategoria(categoria != null && !categoria.isBlank() ? categoria : "Otros");
            g.setFecha(fecha);
            g.setMonto(monto);
            g.setPresupuesto(monto);
            g.setEmprendedor(emprendedor);
            gastoRepository.save(g);
        } else { // compra
            ComprasMenores c = new ComprasMenores();
            c.setDescripcion(descripcion);
            c.setFecha(fecha);
            c.setMonto(monto);
            c.setEmprendedor(emprendedor);
            Categoria cat = categoriaRepository.findById(idCategoria != null ? idCategoria : 7L)
                    .orElse(categoriaRepository.findById(7L).orElse(null));
            c.setCategoria(cat);
            comprasMenoresRepository.save(c);
        }
    }

    public Optional<Gasto> buscarGasto(Long id) {
        return gastoRepository.findById(id);
    }

    @Transactional
    public void actualizarGasto(Long id, String descripcion, String categoria, LocalDate fecha, BigDecimal monto) {
        Gasto g = gastoRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Gasto no encontrado."));
        g.setDescripcion(descripcion);
        g.setCategoria(categoria);
        g.setFecha(fecha);
        g.setMonto(monto);
        gastoRepository.save(g);
    }

    @Transactional
    public void eliminarGasto(Long id) {
        gastoRepository.deleteById(id);
    }
}
