package com.example.demo.repository;

import com.example.demo.modelo.Ingreso;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.math.BigDecimal;
import java.time.LocalDate;

@Repository
public interface IngresoRepository extends JpaRepository<Ingreso, Long> {

    @Query("select coalesce(sum(i.monto), 0) from Ingreso i")
    BigDecimal sumTotalMonto();

    @Query("select coalesce(sum(i.monto), 0) from Ingreso i where i.fecha between :inicio and :fin")
    BigDecimal sumMontoEntre(@Param("inicio") LocalDate inicio, @Param("fin") LocalDate fin);
}
