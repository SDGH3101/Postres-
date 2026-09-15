package com.example.demo.repository;

import com.example.demo.modelo.Emprendedor;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface EmprendedorRepository extends JpaRepository<Emprendedor, Long> {

    boolean existsByIdUsuario(Long idUsuario);

    Optional<Emprendedor> findByIdUsuario(Long idUsuario);

    @Query("select e.idUsuario from Emprendedor e order by e.idUsuario asc")
    java.util.List<Long> findAllIds();
}
