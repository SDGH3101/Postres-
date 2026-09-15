package com.example.demo.repository;

import com.example.demo.modelo.Gasto;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.math.BigDecimal;
import java.util.List;

@Repository
public interface GastoRepository extends JpaRepository<Gasto, Long> {

    /**
     * Gastos con join a emprendedor/usuario.
     * [0]=id, [1]=fecha, [2]=descripcion, [3]=categoria, [4]=monto, [5]=presupuesto, [6]=diferencia, [7]=emprendedor(nombre)
     */
    @Query(value = """
        SELECT g.id_gasto, DATE_FORMAT(g.fecha,'%d-%m-%Y') AS fecha, g.descripcion,
               g.categoria, g.monto, g.presupuesto,
               g.monto - g.presupuesto AS diferencia,
               u.nombre AS emprendedor
        FROM gasto g
        JOIN emprendedor emp ON g.id_emprendedor = emp.id_usuario
        JOIN usuario u ON emp.id_usuario = u.id_usuario
        ORDER BY g.fecha DESC
    """, nativeQuery = true)
    List<Object[]> listarGastos();

    /**
     * Gastos por categoria (sp_gastos_por_categoria).
     * [0]=categoria, [1]=cantidad, [2]=total, [3]=promedio
     */
    @Query(value = """
        SELECT categoria, COUNT(*) AS cantidad, SUM(monto) AS total, AVG(monto) AS promedio
        FROM gasto GROUP BY categoria ORDER BY total DESC
    """, nativeQuery = true)
    List<Object[]> gastosPorCategoria();

    @Query("select coalesce(sum(g.monto), 0) from Gasto g")
    BigDecimal sumTotalMonto();

    @Query("select coalesce(sum(g.monto), 0) from Gasto g where g.fecha between :inicio and :fin")
    BigDecimal sumMontoEntre(@Param("inicio") java.time.LocalDate inicio,
                             @Param("fin") java.time.LocalDate fin);

    @Query("select coalesce(sum(g.monto), 0) from Gasto g where g.emprendedor.idUsuario = :emprendedorId and g.fecha between :inicio and :fin")
    BigDecimal sumMontoEntreDeEmprendedor(@Param("emprendedorId") Long emprendedorId,
                                          @Param("inicio") java.time.LocalDate inicio,
                                          @Param("fin") java.time.LocalDate fin);
}
