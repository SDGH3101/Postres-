package com.example.demo.repository;

import com.example.demo.modelo.Empleado;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface EmpleadoRepository extends JpaRepository<Empleado, Long> {

    boolean existsByIdUsuario(Long idUsuario);

    Optional<Empleado> findByIdUsuario(Long idUsuario);

    /**
     * Lista empleados con metricas de ventas (equivale a sp_listar_empleados).
     * [0]=id, [1]=nombre, [2]=correo, [3]=cargo, [4]=salario, [5]=horas,
     * [6]=total_ventas, [7]=ingresos_generados, [8]=dias_empresa
     */
    @Query(value = """
        SELECT u.id_usuario, u.nombre, u.correo, em.cargo, em.salario,
               em.horas_trabajadas,
               COUNT(v.id_venta) AS total_ventas,
               COALESCE(SUM(v.total), 0) AS ingresos_generados,
               DATEDIFF(CURDATE(), u.registro) AS dias_empresa
        FROM empleado em
        JOIN usuario u ON em.id_usuario = u.id_usuario
        LEFT JOIN venta v ON v.id_empleado = em.id_usuario
        GROUP BY em.id_usuario, u.nombre, u.correo, em.cargo, em.salario,
                 em.horas_trabajadas, u.registro
        ORDER BY em.salario DESC
    """, nativeQuery = true)
    List<Object[]> listarEmpleados();

    /**
     * Rendimiento de un empleado (equivale a sp_rendimiento_empleado).
     * [0]=id, [1]=nombre, [2]=cargo, [3]=salario, [4]=total_ventas,
     * [5]=ingresos_generados, [6]=promedio_venta, [7]=ratio
     */
    @Query(value = """
        SELECT u.id_usuario, u.nombre, em.cargo, em.salario,
               COUNT(v.id_venta) AS total_ventas,
               COALESCE(SUM(v.total), 0) AS ingresos_generados,
               COALESCE(AVG(v.total), 0) AS promedio_venta,
               ROUND(COALESCE(SUM(v.total),0) / NULLIF(em.salario,0), 2) AS ratio
        FROM empleado em
        JOIN usuario u ON em.id_usuario = u.id_usuario
        LEFT JOIN venta v ON v.id_empleado = em.id_usuario
        WHERE em.id_usuario = :id
        GROUP BY em.id_usuario, u.nombre, em.cargo, em.salario
    """, nativeQuery = true)
    Optional<Object[]> rendimientoEmpleado(@Param("id") Long id);
}
