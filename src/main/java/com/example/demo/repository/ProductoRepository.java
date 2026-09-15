package com.example.demo.repository;

import com.example.demo.modelo.Producto;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.time.LocalDate;
import java.util.List;

@Repository
public interface ProductoRepository extends JpaRepository<Producto, Long> {

    List<Producto> findAllByOrderByIdProductoAsc();

    List<Producto> findAllByOrderByDescripcionAsc();

    /**
     * Productos disponibles para la venta: stock > 0 y (sin caducidad o no vencido).
     */
    @Query("""
        select p from Producto p
        where p.stock > 0
          and (p.fechaCaducidad is null or p.fechaCaducidad >= :hoy)
        order by p.descripcion
    """)
    List<Producto> disponiblesParaVenta(@Param("hoy") LocalDate hoy);

    @Query("select p from Producto p where p.stock <= p.stockMinimo")
    List<Producto> buscarStockBajo();

    @Query("select p from Producto p where p.fechaCaducidad is not null and p.fechaCaducidad < :hoy")
    List<Producto> buscarVencidos(@Param("hoy") LocalDate hoy);

    @Query("select p from Producto p where p.fechaCaducidad is not null and p.fechaCaducidad between :inicio and :fin")
    List<Producto> buscarPorVencer(@Param("inicio") LocalDate inicio, @Param("fin") LocalDate fin);
}
