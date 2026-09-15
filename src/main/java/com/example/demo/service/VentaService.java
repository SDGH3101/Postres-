package com.example.demo.service;

import com.example.demo.modelo.DetalleVenta;
import com.example.demo.modelo.Empleado;
import com.example.demo.modelo.Producto;
import com.example.demo.modelo.Venta;
import com.example.demo.repository.DetalleVentaRepository;
import com.example.demo.repository.EmpleadoRepository;
import com.example.demo.repository.ProductoRepository;
import com.example.demo.repository.VentaRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

/**
 * Registro de ventas con carrito multi-producto.
 * Los triggers de la base de datos (trg_descontar_stock y trg_ingreso_por_venta)
 * se encargan de descontar stock y generar el ingreso automaticamente al
 * insertar cada linea de detalle_venta.
 */
@Service
public class VentaService {

    private final VentaRepository ventaRepository;
    private final DetalleVentaRepository detalleVentaRepository;
    private final ProductoRepository productoRepository;
    private final EmpleadoRepository empleadoRepository;

    public VentaService(VentaRepository ventaRepository,
                        DetalleVentaRepository detalleVentaRepository,
                        ProductoRepository productoRepository,
                        EmpleadoRepository empleadoRepository) {
        this.ventaRepository = ventaRepository;
        this.detalleVentaRepository = detalleVentaRepository;
        this.productoRepository = productoRepository;
        this.empleadoRepository = empleadoRepository;
    }

    public List<Object[]> listarVentas() {
        return ventaRepository.listarVentas();
    }

    public List<Producto> productosVenta() {
        return productoRepository.disponiblesParaVenta(LocalDate.now());
    }

    public List<Empleado> empleados() {
        return empleadoRepository.findAll();
    }

    /**
     * Registra una venta con varias lineas. Valida stock y caducidad antes de insertar.
     * La insercion es transaccional; los triggers descuentan stock y crean el ingreso.
     */
    @Transactional
    public void registrarVenta(Long idEmpleado, LocalDate fecha, String medioPago, List<ItemVenta> items) {
        if (items == null || items.isEmpty()) {
            throw new IllegalArgumentException("Agrega al menos un producto al carrito.");
        }

        // Validar cada linea antes de insertar nada
        List<DetalleVenta> lineas = new ArrayList<>();
        BigDecimal total = BigDecimal.ZERO;

        for (ItemVenta item : items) {
            Producto producto = productoRepository.findById(item.getIdProducto())
                    .orElseThrow(() -> new IllegalArgumentException("Producto no encontrado."));

            if (producto.getStock() < item.getCantidad()) {
                throw new IllegalArgumentException(
                    "Stock insuficiente para " + producto.getDescripcion() +
                    " (disponible: " + producto.getStock() + ").");
            }
            if (producto.getFechaCaducidad() != null && producto.getFechaCaducidad().isBefore(LocalDate.now())) {
                throw new IllegalArgumentException(
                    producto.getDescripcion() + " está vencido desde " +
                    producto.getFechaCaducidad() + " y no se puede vender.");
            }

            BigDecimal precioUnitario = producto.getPrecio();
            BigDecimal subtotal = precioUnitario.multiply(BigDecimal.valueOf(item.getCantidad()))
                    .setScale(2, RoundingMode.HALF_UP);
            total = total.add(subtotal);

            DetalleVenta detalle = new DetalleVenta();
            detalle.setProducto(producto);
            detalle.setCantidad(item.getCantidad());
            detalle.setPrecioUnitario(precioUnitario);
            detalle.setSubtotal(subtotal);
            lineas.add(detalle);
        }

        Empleado empleado = empleadoRepository.findByIdUsuario(idEmpleado)
                .orElseThrow(() -> new IllegalArgumentException("Empleado responsable no válido."));

        Venta venta = new Venta();
        venta.setFecha(fecha);
        venta.setTotal(total);
        venta.setMedioPago(medioPago);
        venta.setEmpleado(empleado);
        venta = ventaRepository.save(venta);

        for (DetalleVenta detalle : lineas) {
            venta.agregarDetalle(detalle);
            detalleVentaRepository.save(detalle);
        }
    }

    @Transactional
    public void eliminarVenta(Long id) {
        // El trigger trg_before_borrar_venta borra detalle_venta y restaura stock
        ventaRepository.deleteById(id);
    }

    public Optional<Venta> buscar(Long id) {
        return ventaRepository.findById(id);
    }

    /**
     * Item del carrito (producto + cantidad).
     */
    public static class ItemVenta {
        private Long idProducto;
        private Integer cantidad;

        public ItemVenta() {
        }

        public ItemVenta(Long idProducto, Integer cantidad) {
            this.idProducto = idProducto;
            this.cantidad = cantidad;
        }

        public Long getIdProducto() {
            return idProducto;
        }

        public void setIdProducto(Long idProducto) {
            this.idProducto = idProducto;
        }

        public Integer getCantidad() {
            return cantidad;
        }

        public void setCantidad(Integer cantidad) {
            this.cantidad = cantidad;
        }
    }
}
