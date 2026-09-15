-- ============================================================================
--  POSTRES LAURA - SCRIPT CONSOLIDADO PARA SPRING BOOT
--  Esquema FINAL (aplica tabla detalle_venta, multi-producto, presupuesto,
--  triggers sobre detalle_venta, stored procedures actualizados).
--  Generado a partir de las migraciones de Laravel del proyecto original.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS postres
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE postres;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS presupuesto, compras_menores, gasto, ingreso, detalle_venta, venta, producto, categoria, cliente, empleado, emprendedor, usuario;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. USUARIO
-- ----------------------------------------------------------------------------
CREATE TABLE usuario (
  id_usuario       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre           VARCHAR(100) NOT NULL,
  correo           VARCHAR(100) NOT NULL UNIQUE,
  contrasena       VARCHAR(255) NOT NULL,
  edad             TINYINT UNSIGNED NULL,
  fecha_nacimiento DATE NULL,
  registro         DATE NULL,
  PRIMARY KEY (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. EMPRENDEDOR
CREATE TABLE emprendedor (
  id_usuario  BIGINT UNSIGNED NOT NULL,
  descripcion VARCHAR(300) NULL,
  PRIMARY KEY (id_usuario),
  CONSTRAINT fk_emp_usu FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. EMPLEADO
CREATE TABLE empleado (
  id_usuario       BIGINT UNSIGNED NOT NULL,
  cargo            VARCHAR(80) NULL,
  salario          DECIMAL(12,2) NOT NULL DEFAULT 0,
  horas_trabajadas SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id_usuario),
  CONSTRAINT fk_empl_usu FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. CLIENTE
CREATE TABLE cliente (
  id_usuario BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_usuario),
  CONSTRAINT fk_cli_usu FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. CATEGORIA
CREATE TABLE categoria (
  id_categoria     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_categoria VARCHAR(80) NOT NULL,
  PRIMARY KEY (id_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. PRODUCTO (multi-producto: con tipo, stock_minimo, fecha_caducidad)
CREATE TABLE producto (
  id_producto     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  descripcion     VARCHAR(200) NOT NULL,
  tipo            ENUM('postre','bebida','otro') NOT NULL DEFAULT 'postre',
  precio          DECIMAL(12,2) NOT NULL DEFAULT 0,
  stock           INT UNSIGNED NOT NULL DEFAULT 0,
  fecha_caducidad DATE NULL,
  stock_minimo    INT UNSIGNED NOT NULL DEFAULT 5,
  PRIMARY KEY (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. VENTA (ya NO lleva id_producto -> se mueve a detalle_venta)
CREATE TABLE venta (
  id_venta    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha       DATE NOT NULL,
  total       DECIMAL(12,2) NOT NULL DEFAULT 0,
  medio_pago  ENUM('efectivo','transferencia_nequi','transferencia_daviplata','transferencia_bancaria','tarjeta_credito','tarjeta_debito') NOT NULL DEFAULT 'efectivo',
  id_empleado BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_venta),
  CONSTRAINT fk_venta_empl FOREIGN KEY (id_empleado) REFERENCES empleado(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. DETALLE VENTA (carrito multi-producto)
CREATE TABLE detalle_venta (
  id_detalle_venta BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_venta         BIGINT UNSIGNED NOT NULL,
  id_producto      BIGINT UNSIGNED NOT NULL,
  cantidad         INT UNSIGNED NOT NULL DEFAULT 1,
  precio_unitario  DECIMAL(12,2) NOT NULL DEFAULT 0,
  subtotal         DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (id_detalle_venta),
  CONSTRAINT fk_dv_venta    FOREIGN KEY (id_venta)    REFERENCES venta(id_venta)      ON DELETE CASCADE,
  CONSTRAINT fk_dv_producto FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. INGRESO (generado por trigger al registrar venta)
CREATE TABLE ingreso (
  id_ingreso       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha            DATE NOT NULL,
  monto            DECIMAL(12,2) NOT NULL DEFAULT 0,
  concepto         VARCHAR(200) NULL,
  id_producto      BIGINT UNSIGNED NULL,
  id_detalle_venta BIGINT UNSIGNED NULL,
  PRIMARY KEY (id_ingreso),
  CONSTRAINT fk_ing_producto FOREIGN KEY (id_producto)      REFERENCES producto(id_producto)      ON DELETE SET NULL,
  CONSTRAINT fk_ing_detalle  FOREIGN KEY (id_detalle_venta) REFERENCES detalle_venta(id_detalle_venta) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. GASTO
CREATE TABLE gasto (
  id_gasto       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  descripcion    VARCHAR(200) NOT NULL,
  categoria      VARCHAR(80) NOT NULL DEFAULT 'Otros',
  fecha          DATE NOT NULL,
  monto          DECIMAL(12,2) NOT NULL DEFAULT 0,
  presupuesto    DECIMAL(12,2) NOT NULL DEFAULT 0,
  id_emprendedor BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_gasto),
  CONSTRAINT fk_gasto_emp FOREIGN KEY (id_emprendedor) REFERENCES emprendedor(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. COMPRAS_MENORES
CREATE TABLE compras_menores (
  id_compra      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  descripcion    VARCHAR(200) NOT NULL,
  fecha          DATE NOT NULL,
  monto          DECIMAL(12,2) NOT NULL DEFAULT 0,
  id_emprendedor BIGINT UNSIGNED NOT NULL,
  id_categoria   BIGINT UNSIGNED NULL,
  PRIMARY KEY (id_compra),
  CONSTRAINT fk_cm_emp       FOREIGN KEY (id_emprendedor) REFERENCES emprendedor(id_usuario) ON DELETE RESTRICT,
  CONSTRAINT fk_cm_cat       FOREIGN KEY (id_categoria)   REFERENCES categoria(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. PRESUPUESTO
CREATE TABLE presupuesto (
  id_presupuesto  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_emprendedor  BIGINT UNSIGNED NOT NULL,
  tipo            ENUM('diario','semanal','mensual','anual') NOT NULL,
  monto           DECIMAL(12,2) NOT NULL DEFAULT 0,
  fecha_inicio    DATE NOT NULL,
  activo          TINYINT(1) NOT NULL DEFAULT 1,
  creado_en       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_presupuesto),
  CONSTRAINT fk_presup_emp FOREIGN KEY (id_emprendedor) REFERENCES emprendedor(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- TRIGGERS (vigentes, sobre detalle_venta - multi-producto)
-- ----------------------------------------------------------------------------

DELIMITER $$

DROP TRIGGER IF EXISTS trg_descontar_stock$$
CREATE TRIGGER trg_descontar_stock
AFTER INSERT ON detalle_venta
FOR EACH ROW
BEGIN
  UPDATE producto SET stock = stock - NEW.cantidad WHERE id_producto = NEW.id_producto;
END$$

DROP TRIGGER IF EXISTS trg_ingreso_por_venta$$
CREATE TRIGGER trg_ingreso_por_venta
AFTER INSERT ON detalle_venta
FOR EACH ROW
BEGIN
  INSERT INTO ingreso (fecha, monto, concepto, id_producto, id_detalle_venta)
  SELECT v.fecha, NEW.subtotal, 'Venta automática', NEW.id_producto, NEW.id_detalle_venta
  FROM venta v WHERE v.id_venta = NEW.id_venta;
END$$

DROP TRIGGER IF EXISTS trg_restaurar_stock_venta$$
CREATE TRIGGER trg_restaurar_stock_venta
AFTER DELETE ON detalle_venta
FOR EACH ROW
BEGIN
  UPDATE producto SET stock = stock + OLD.cantidad WHERE id_producto = OLD.id_producto;
END$$

-- Necesario porque MariaDB/MySQL no dispara triggers AFTER DELETE en tabla hija
-- cuando el borrado ocurre por ON DELETE CASCADE. Al borrar explicitamente
-- detalle_venta aqui si se dispara trg_restaurar_stock_venta.
DROP TRIGGER IF EXISTS trg_before_borrar_venta$$
CREATE TRIGGER trg_before_borrar_venta
BEFORE DELETE ON venta
FOR EACH ROW
BEGIN
  DELETE FROM detalle_venta WHERE id_venta = OLD.id_venta;
END$$

DELIMITER ;

-- ----------------------------------------------------------------------------
-- STORED PROCEDURES
-- ----------------------------------------------------------------------------

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_listar_ventas$$
CREATE PROCEDURE sp_listar_ventas()
BEGIN
  SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha, u.nombre AS empleado,
         em.cargo, v.total, v.medio_pago,
         GROUP_CONCAT(CONCAT(p.descripcion,' x',d.cantidad) SEPARATOR ', ') AS productos
  FROM venta v
  JOIN empleado em ON v.id_empleado = em.id_usuario
  JOIN usuario  u  ON em.id_usuario = u.id_usuario
  JOIN detalle_venta d ON d.id_venta = v.id_venta
  JOIN producto p ON d.id_producto = p.id_producto
  GROUP BY v.id_venta, v.fecha, u.nombre, em.cargo, v.total, v.medio_pago
  ORDER BY v.fecha DESC;
END$$

DROP PROCEDURE IF EXISTS sp_listar_empleados$$
CREATE PROCEDURE sp_listar_empleados()
BEGIN
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
  ORDER BY em.salario DESC;
END$$

DROP PROCEDURE IF EXISTS sp_rendimiento_empleado$$
CREATE PROCEDURE sp_rendimiento_empleado(IN p_id BIGINT)
BEGIN
  SELECT u.id_usuario, u.nombre, em.cargo, em.salario,
         COUNT(v.id_venta) AS total_ventas,
         COALESCE(SUM(v.total), 0) AS ingresos_generados,
         COALESCE(AVG(v.total), 0) AS promedio_venta,
         ROUND(COALESCE(SUM(v.total),0) / NULLIF(em.salario,0), 2) AS ratio
  FROM empleado em
  JOIN usuario u ON em.id_usuario = u.id_usuario
  LEFT JOIN venta v ON v.id_empleado = em.id_usuario
  WHERE em.id_usuario = p_id
  GROUP BY em.id_usuario, u.nombre, em.cargo, em.salario;
END$$

DROP PROCEDURE IF EXISTS sp_ventas_por_mes$$
CREATE PROCEDURE sp_ventas_por_mes()
BEGIN
  SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes,
         COUNT(*) AS total_ventas,
         SUM(total) AS ingresos_mes,
         AVG(total) AS ticket_promedio
  FROM venta
  GROUP BY DATE_FORMAT(fecha,'%m-%Y')
  ORDER BY mes;
END$$

DROP PROCEDURE IF EXISTS sp_gastos_por_categoria$$
CREATE PROCEDURE sp_gastos_por_categoria()
BEGIN
  SELECT categoria, COUNT(*) AS cantidad, SUM(monto) AS total, AVG(monto) AS promedio
  FROM gasto GROUP BY categoria ORDER BY total DESC;
END$$

DROP PROCEDURE IF EXISTS sp_productos_mas_vendidos$$
CREATE PROCEDURE sp_productos_mas_vendidos()
BEGIN
  SELECT p.descripcion, p.precio, p.stock,
         COUNT(d.id_detalle_venta) AS veces,
         SUM(d.subtotal) AS ingresos
  FROM producto p
  JOIN detalle_venta d ON d.id_producto = p.id_producto
  GROUP BY p.id_producto, p.descripcion, p.precio, p.stock
  ORDER BY veces DESC;
END$$

DROP PROCEDURE IF EXISTS sp_balance_ventas_gastos$$
CREATE PROCEDURE sp_balance_ventas_gastos()
BEGIN
  SELECT ventas.mes,
         ventas.total_ventas AS ingresos,
         COALESCE(gastos.total_gastos, 0) AS gastos,
         ventas.total_ventas - COALESCE(gastos.total_gastos, 0) AS balance
  FROM (
    SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes, SUM(total) AS total_ventas
    FROM venta GROUP BY DATE_FORMAT(fecha,'%m-%Y')
  ) ventas
  LEFT JOIN (
    SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes, SUM(monto) AS total_gastos
    FROM gasto GROUP BY DATE_FORMAT(fecha,'%m-%Y')
  ) gastos ON ventas.mes = gastos.mes
  ORDER BY ventas.mes;
END$$

DELIMITER ;

-- ----------------------------------------------------------------------------
-- DATOS DE PRUEBA
-- Nota: la contrasena de todos los usuarios es "password"
-- (hash bcrypt generado con BCryptPasswordEncoder de Spring Security / prefijo $2a$)
-- ----------------------------------------------------------------------------

INSERT INTO usuario (nombre, correo, contrasena, edad, fecha_nacimiento, registro) VALUES
('Laura Martínez', 'laura@postres.com', '$2a$10$w5KkAYwqKaAZJn4VmpAegOJJQ7vtuUQ2kiScWDPPAz9Z3XaYif1t2', 32, '1994-01-15', '2023-01-10'),
('David Gómez',    'david@postres.com', '$2a$10$Kl2yCRielAC0mzbqMZ1G3uDbfujbu7A51LNDdgyAdhLski0fTqedG', 25, '1999-03-20', '2023-03-15'),
('María López',    'maria@email.com',   '$2a$10$N8pLSjIHMd2gAk00MAuot.kdphFhLNDo0GOT0Gsq4g64SffWu4vV.', 28, '1998-06-01', '2023-06-01');

INSERT INTO emprendedor (id_usuario, descripcion) VALUES (1, 'Postres Laura');

INSERT INTO empleado (id_usuario, cargo, salario, horas_trabajadas) VALUES (2, 'Pastelero', 1800000, 160);

INSERT INTO cliente (id_usuario) VALUES (3);

INSERT INTO categoria (nombre_categoria) VALUES
('Lácteos'),('Harinas'),('Azúcares'),('Frutas'),('Chocolates'),('Esencias'),('Otros');

INSERT INTO producto (descripcion, tipo, precio, stock, stock_minimo) VALUES
('Torta de Chocolate',  'postre', 85000, 12, 3),
('Cupcakes x6',         'postre', 32000, 30, 5),
('Cheesecake de Fresas','postre', 68000,  8, 2),
('Macarons x12',        'postre', 45000, 25, 5),
('Brownie de Nuez',     'postre', 28000, 40, 5),
('Torta de Tres Leches','postre', 75000,  6, 2),
('Limonada Natural',    'bebida',  8000, 50, 10),
('Café con Leche',      'bebida',  5000, 60, 10);

INSERT INTO venta (fecha, total, medio_pago, id_empleado) VALUES
('2024-10-05', 85000, 'efectivo', 2),
('2024-10-12', 32000, 'tarjeta_debito', 2),
('2024-11-03', 68000, 'efectivo', 2),
('2024-11-18', 45000, 'transferencia_nequi', 2),
('2024-12-02', 85000, 'efectivo', 2),
('2024-12-20', 75000, 'tarjeta_credito', 2),
('2025-01-08', 28000, 'efectivo', 2),
('2025-01-22', 32000, 'transferencia_bancaria', 2);

INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 1, 85000, 85000),
(2, 2, 1, 32000, 32000),
(3, 3, 1, 68000, 68000),
(4, 4, 1, 45000, 45000),
(5, 1, 1, 85000, 85000),
(6, 6, 1, 75000, 75000),
(7, 5, 1, 28000, 28000),
(8, 2, 1, 32000, 32000);

INSERT INTO gasto (descripcion, categoria, fecha, monto, presupuesto, id_emprendedor) VALUES
('Luz y agua octubre', 'Servicios', '2024-10-01', 180000, 200000, 1),
('Insumos noviembre',  'Insumos',   '2024-11-01', 350000, 400000, 1),
('Salario diciembre',  'Nómina',    '2024-12-01', 1800000, 1800000, 1);

INSERT INTO compras_menores (descripcion, fecha, monto, id_emprendedor, id_categoria) VALUES
('Fresas 2kg',       '2024-10-10', 18000, 1, 4),
('Harina 5kg',       '2024-11-05', 22000, 1, 2),
('Chocolate amargo', '2024-12-03', 35000, 1, 5);
