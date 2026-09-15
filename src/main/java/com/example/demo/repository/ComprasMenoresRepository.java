package com.example.demo.repository;

import com.example.demo.modelo.ComprasMenores;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;

@Repository
public interface ComprasMenoresRepository extends JpaRepository<ComprasMenores, Long> {

    /**
     * Compras menores con join a categoria.
     * [0]=id, [1]=fecha, [2]=descripcion, [3]=monto, [4]=categoria(nombre)
     */
    @Query(value = """
        SELECT cm.id_compra, DATE_FORMAT(cm.fecha,'%d-%m-%Y') AS fecha,
               cm.descripcion, cm.monto, cat.nombre_categoria AS categoria
        FROM compras_menores cm
        JOIN categoria cat ON cm.id_categoria = cat.id_categoria
        ORDER BY cm.fecha DESC
    """, nativeQuery = true)
    List<Object[]> listarCompras();

    @Query("select coalesce(sum(c.monto), 0) from ComprasMenores c")
    BigDecimal sumTotalMonto();

    @Query("select coalesce(sum(c.monto), 0) from ComprasMenores c where c.fecha between :inicio and :fin")
    BigDecimal sumMontoEntre(@Param("inicio") LocalDate inicio, @Param("fin") LocalDate fin);

    @Query("select coalesce(sum(c.monto), 0) from ComprasMenores c where c.emprendedor.idUsuario = :emprendedorId and c.fecha between :inicio and :fin")
    BigDecimal sumMontoEntreDeEmprendedor(@Param("emprendedorId") Long emprendedorId,
                                          @Param("inicio") LocalDate inicio,
                                          @Param("fin") LocalDate fin);
}
