package com.example.demo.repository;

import com.example.demo.modelo.Usuario;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface UsuarioRepository extends JpaRepository<Usuario, Long> {

    Optional<Usuario> findByCorreo(String correo);

    boolean existsByCorreo(String correo);

    /**
     * Lista usuarios con su rol resuelto (equivalente a sp_listar_usuarios del proyecto Laravel).
     * Devuelve: [0]=id, [1]=nombre, [2]=correo, [3]=edad, [4]=fecha_registro, [5]=rol, [6]=empresa, [7]=cargo
     */
    @Query(value = """
        SELECT u.id_usuario, u.nombre, u.correo, u.edad,
               DATE_FORMAT(u.registro,'%d-%m-%Y') AS fecha_registro,
               CASE WHEN emp.id_usuario IS NOT NULL THEN 'emprendedor'
                    WHEN em.id_usuario  IS NOT NULL THEN 'empleado'
                    WHEN c.id_usuario   IS NOT NULL THEN 'cliente'
                    ELSE 'sin rol' END AS rol,
               emp.descripcion AS empresa, em.cargo
        FROM usuario u
        LEFT JOIN emprendedor emp ON u.id_usuario = emp.id_usuario
        LEFT JOIN empleado em     ON u.id_usuario = em.id_usuario
        LEFT JOIN cliente c       ON u.id_usuario = c.id_usuario
        ORDER BY u.id_usuario
    """, nativeQuery = true)
    List<Object[]> listarUsuariosConRol();

    @Query(value = """
        SELECT u.id_usuario, u.nombre, emp.descripcion AS empresa,
               em.cargo, em.salario
        FROM usuario u
        LEFT JOIN emprendedor emp ON u.id_usuario = emp.id_usuario
        LEFT JOIN empleado em     ON u.id_usuario = em.id_usuario
        LEFT JOIN cliente c       ON u.id_usuario = c.id_usuario
        WHERE u.id_usuario = :id
    """, nativeQuery = true)
    Optional<Object[]> buscarUsuarioConRol(@Param("id") Long id);
}
