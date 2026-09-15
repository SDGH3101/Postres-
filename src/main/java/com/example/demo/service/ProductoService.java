package com.example.demo.service;

import com.example.demo.modelo.Producto;
import com.example.demo.repository.ProductoRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

/**
 * Gestion de productos (equivale a ProductoController de Laravel - emprendedor/empleado).
 */
@Service
public class ProductoService {

    private final ProductoRepository productoRepository;

    public ProductoService(ProductoRepository productoRepository) {
        this.productoRepository = productoRepository;
    }

    public List<Producto> listar() {
        return productoRepository.findAllByOrderByIdProductoAsc();
    }

    public List<Producto> catalogo() {
        return productoRepository.findAllByOrderByDescripcionAsc();
    }

    public List<Producto> disponiblesParaVenta() {
        return productoRepository.disponiblesParaVenta(LocalDate.now());
    }

    public List<Producto> stockBajo() {
        return productoRepository.buscarStockBajo();
    }

    public List<Producto> vencidos() {
        return productoRepository.buscarVencidos(LocalDate.now());
    }

    public List<Producto> porVencer(int dias) {
        return productoRepository.buscarPorVencer(LocalDate.now(), LocalDate.now().plusDays(dias));
    }

    public Optional<Producto> buscar(Long id) {
        return productoRepository.findById(id);
    }

    @Transactional
    public void crear(String descripcion, String tipo, BigDecimal precio,
                      Integer stock, Integer stockMinimo, LocalDate fechaCaducidad) {
        Producto p = new Producto();
        p.setDescripcion(descripcion);
        p.setTipo(tipo);
        p.setPrecio(precio);
        p.setStock(stock);
        p.setStockMinimo(stockMinimo);
        p.setFechaCaducidad(fechaCaducidad);
        productoRepository.save(p);
    }

    @Transactional
    public void actualizar(Long id, String descripcion, String tipo, BigDecimal precio,
                           Integer stock, Integer stockMinimo, LocalDate fechaCaducidad) {
        Producto p = productoRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Producto no encontrado."));
        p.setDescripcion(descripcion);
        p.setTipo(tipo);
        p.setPrecio(precio);
        p.setStock(stock);
        p.setStockMinimo(stockMinimo);
        p.setFechaCaducidad(fechaCaducidad);
        productoRepository.save(p);
    }

    @Transactional
    public void eliminar(Long id) {
        productoRepository.deleteById(id);
    }
}
