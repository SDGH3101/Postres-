package com.example.demo.repository;

import com.example.demo.modelo.Presupuesto;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface PresupuestoRepository extends JpaRepository<Presupuesto, Long> {

    List<Presupuesto> findByEmprendedor_IdUsuarioAndActivoTrueOrderByTipoAsc(Long emprendedorId);

    void deleteByIdPresupuestoAndEmprendedor_IdUsuario(Long idPresupuesto, Long emprendedorId);
}
