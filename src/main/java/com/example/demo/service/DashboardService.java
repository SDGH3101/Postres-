package com.example.demo.service;

import com.example.demo.repository.GastoRepository;
import com.example.demo.repository.IngresoRepository;
import com.example.demo.repository.ProductoRepository;
import com.example.demo.repository.UsuarioRepository;
import com.example.demo.repository.VentaRepository;
import com.example.demo.repository.ComprasMenoresRepository;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/**
 * Datos para el dashboard (equivale a DashboardController de Laravel).
 */
@Service
public class DashboardService {

    private final ProductoRepository productoRepository;
    private final UsuarioRepository usuarioRepository;
    private final VentaRepository ventaRepository;
    private final IngresoRepository ingresoRepository;
    private final GastoRepository gastoRepository;
    private final ComprasMenoresRepository comprasMenoresRepository;

    public DashboardService(ProductoRepository productoRepository,
                            UsuarioRepository usuarioRepository,
                            VentaRepository ventaRepository,
                            IngresoRepository ingresoRepository,
                            GastoRepository gastoRepository,
                            ComprasMenoresRepository comprasMenoresRepository) {
        this.productoRepository = productoRepository;
        this.usuarioRepository = usuarioRepository;
        this.ventaRepository = ventaRepository;
        this.ingresoRepository = ingresoRepository;
        this.gastoRepository = gastoRepository;
        this.comprasMenoresRepository = comprasMenoresRepository;
    }

    public Map<String, Object> metricas() {
        Map<String, Object> m = new LinkedHashMap<>();
        m.put("totalProductos", productoRepository.count());
        m.put("totalUsuarios", usuarioRepository.count());
        m.put("ventasRecientes", lista(ventaRepository.ultimasVentas()));
        m.put("totalIngresos", monto(ingresoRepository.sumTotalMonto()));
        BigDecimal gastos = monto(gastoRepository.sumTotalMonto())
                .add(monto(comprasMenoresRepository.sumTotalMonto()));
        m.put("totalGastos", gastos);
        m.put("ventasMes", lista(ventaRepository.ventasPorMes()));
        m.put("masVendidos", lista(ventaRepository.productosMasVendidos()));
        return m;
    }

    /** MySQL devuelve NULL cuando SUM() no encuentra filas: normaliza a BigDecimal.ZERO. */
    private BigDecimal monto(BigDecimal valor) {
        return valor != null ? valor : BigDecimal.ZERO;
    }

    /** Nunca exponer una lista nula a Thymeleaf (th:each sobre null lanza 500). */
    private List<Object[]> lista(List<Object[]> datos) {
        return datos != null ? datos : List.of();
    }

    public BigDecimal totalIngresos() {
        return monto(ingresoRepository.sumTotalMonto());
    }
}
