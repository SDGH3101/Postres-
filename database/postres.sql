--  POSTRES LAURA — SQL 

CREATE DATABASE IF NOT EXISTS postres
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE postres;

-- ── 1. USUARIO ───────────────────────────────────────────────
CREATE TABLE usuario (
  id_usuario  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(100)    NOT NULL,
  correo      VARCHAR(100)    NOT NULL UNIQUE,
  contrasena  VARCHAR(255)    NOT NULL,
  edad        TINYINT UNSIGNED NULL,
  registro    DATE            NULL,
  PRIMARY KEY (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 2. EMPRENDEDOR ───────────────────────────────────────────
CREATE TABLE emprendedor (
  id_usuario  BIGINT UNSIGNED NOT NULL,
  descripcion VARCHAR(300)    NULL,
  PRIMARY KEY (id_usuario),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 3. EMPLEADO ──────────────────────────────────────────────
CREATE TABLE empleado (
  id_usuario       BIGINT UNSIGNED  NOT NULL,
  cargo            VARCHAR(80)      NULL,
  salario          DECIMAL(12,2)    NOT NULL DEFAULT 0,
  horas_trabajadas SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id_usuario),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 4. CLIENTE ───────────────────────────────────────────────
CREATE TABLE cliente (
  id_usuario BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_usuario),
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 5. CATEGORIA ─────────────────────────────────────────────
CREATE TABLE categoria (
  id_categoria    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_categoria VARCHAR(80)    NOT NULL,
  PRIMARY KEY (id_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 6. PRODUCTO ──────────────────────────────────────────────
CREATE TABLE producto (
  id_producto BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  descripcion VARCHAR(200)    NOT NULL,
  precio      DECIMAL(12,2)   NOT NULL DEFAULT 0,
  stock       INT UNSIGNED    NOT NULL DEFAULT 0,
  PRIMARY KEY (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 7. VENTA ─────────────────────────────────────────────────
CREATE TABLE venta (
  id_venta    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha       DATE            NOT NULL,
  total       DECIMAL(12,2)   NOT NULL DEFAULT 0,
  id_empleado BIGINT UNSIGNED NOT NULL,
  id_producto BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_venta),
  FOREIGN KEY (id_empleado) REFERENCES empleado(id_usuario) ON DELETE RESTRICT,
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 8. INGRESO ───────────────────────────────────────────────
CREATE TABLE ingreso (
  id_ingreso  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha       DATE            NOT NULL,
  monto       DECIMAL(12,2)   NOT NULL DEFAULT 0,
  concepto    VARCHAR(200)    NULL,
  id_producto BIGINT UNSIGNED NULL,
  PRIMARY KEY (id_ingreso),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 9. GASTO ─────────────────────────────────────────────────
CREATE TABLE gasto (
  id_gasto       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  descripcion    VARCHAR(200)    NOT NULL,
  categoria      VARCHAR(80)     NOT NULL DEFAULT 'Otros',
  fecha          DATE            NOT NULL,
  monto          DECIMAL(12,2)   NOT NULL DEFAULT 0,
  presupuesto    DECIMAL(12,2)   NOT NULL DEFAULT 0,
  id_emprendedor BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (id_gasto),
  FOREIGN KEY (id_emprendedor) REFERENCES emprendedor(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 10. COMPRAS_MENORES ──────────────────────────────────────
CREATE TABLE compras_menores (
  id_compra      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  descripcion    VARCHAR(200)    NOT NULL,
  fecha          DATE            NOT NULL,
  monto          DECIMAL(12,2)   NOT NULL DEFAULT 0,
  id_emprendedor BIGINT UNSIGNED NOT NULL,
  id_categoria   BIGINT UNSIGNED NULL,
  PRIMARY KEY (id_compra),
  FOREIGN KEY (id_emprendedor) REFERENCES emprendedor(id_usuario) ON DELETE RESTRICT,
  FOREIGN KEY (id_categoria)   REFERENCES categoria(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TRIGGERS ─────────────────────────────────────────────────

-- Trigger: descontar stock al registrar venta
DELIMITER $$
CREATE TRIGGER trg_descontar_stock
AFTER INSERT ON venta
FOR EACH ROW
BEGIN
  UPDATE producto
  SET stock = stock - 1
  WHERE id_producto = NEW.id_producto;
END$$

-- Trigger: registrar ingreso automáticamente al vender
CREATE TRIGGER trg_ingreso_por_venta
AFTER INSERT ON venta
FOR EACH ROW
BEGIN
  INSERT INTO ingreso (fecha, monto, concepto, id_producto)
  VALUES (NEW.fecha, NEW.total, 'Venta automática', NEW.id_producto);
END$$

DELIMITER ;

-- ── STORED PROCEDURES ────────────────────────────────────────

DELIMITER $$

-- sp_listar_ventas
CREATE PROCEDURE sp_listar_ventas()
BEGIN
  SELECT v.id_venta, DATE_FORMAT(v.fecha,'%d-%m-%Y') AS fecha,
         u.nombre AS empleado, em.cargo,
         p.descripcion AS producto, v.total
  FROM venta v
  JOIN empleado em ON v.id_empleado = em.id_usuario
  JOIN usuario  u  ON em.id_usuario = u.id_usuario
  JOIN producto p  ON v.id_producto = p.id_producto
  ORDER BY v.fecha DESC;
END$$

-- sp_listar_empleados con métricas
CREATE PROCEDURE sp_listar_empleados()
BEGIN
  SELECT u.id_usuario, u.nombre, u.correo, em.cargo, em.salario,
         em.horas_trabajadas,
         COUNT(v.id_venta)  AS total_ventas,
         COALESCE(SUM(v.total), 0) AS ingresos_generados,
         COALESCE(AVG(v.total), 0) AS promedio_venta,
         DATEDIFF(CURDATE(), u.registro) AS dias_empresa
  FROM empleado em
  JOIN usuario u ON em.id_usuario = u.id_usuario
  LEFT JOIN venta v ON v.id_empleado = em.id_usuario
  GROUP BY em.id_usuario;
END$$

-- sp_rendimiento_empleado
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
  GROUP BY em.id_usuario;
END$$

-- sp_ventas_por_mes
CREATE PROCEDURE sp_ventas_por_mes()
BEGIN
  SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes,
         COUNT(*)   AS total_ventas,
         SUM(total) AS ingresos_mes,
         AVG(total) AS ticket_promedio
  FROM venta
  GROUP BY DATE_FORMAT(fecha,'%m-%Y')
  ORDER BY mes;
END$$

-- sp_gastos_por_categoria
CREATE PROCEDURE sp_gastos_por_categoria()
BEGIN
  SELECT categoria, COUNT(*) AS cantidad,
         SUM(monto) AS total, AVG(monto) AS promedio
  FROM gasto
  GROUP BY categoria
  ORDER BY total DESC;
END$$

-- sp_productos_mas_vendidos
CREATE PROCEDURE sp_productos_mas_vendidos()
BEGIN
  SELECT p.descripcion, p.precio, p.stock,
         COUNT(v.id_venta) AS veces,
         COALESCE(SUM(v.total), 0) AS ingresos
  FROM producto p
  LEFT JOIN venta v ON v.id_producto = p.id_producto
  GROUP BY p.id_producto
  ORDER BY veces DESC;
END$$

-- sp_balance_ventas_gastos
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

-- ── DATOS DE PRUEBA ──────────────────────────────────────────

INSERT INTO usuario (nombre, correo, contrasena, edad, registro) VALUES
('Laura Martínez', 'laura@postres.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usc6P4Gm.', 32, '2023-01-10'),
('David Gómez',    'david@postres.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usc6P4Gm.', 25, '2023-03-15'),
('María López',    'maria@email.com',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usc6P4Gm.', 28, '2023-06-01');

-- Nota: el hash corresponde a la contraseña "password"
-- Para usar las contraseñas reales (laura123, david456, maria789)
-- ejecuta: php artisan db:seed  (usa Hash::make de Laravel)

INSERT INTO emprendedor (id_usuario, descripcion) VALUES (1, 'Fundadora de Postres Laura');
INSERT INTO empleado (id_usuario, cargo, salario, horas_trabajadas) VALUES (2, 'Pastelero', 1800000, 160);
INSERT INTO cliente (id_usuario) VALUES (3);

INSERT INTO categoria (nombre_categoria) VALUES
('Lácteos'),('Harinas'),('Azúcares'),('Frutas'),('Chocolates'),('Esencias'),('Otros');

INSERT INTO producto (descripcion, precio, stock) VALUES
('Torta de Chocolate',  85000, 12),
('Cupcakes x6',         32000, 30),
('Cheesecake de Fresas',68000,  8),
('Macarons x12',        45000, 25),
('Brownie de Nuez',     28000, 40),
('Torta de Tres Leches',75000,  6);

INSERT INTO venta (fecha, total, id_empleado, id_producto) VALUES
('2024-10-05', 85000, 2, 1),
('2024-10-12', 32000, 2, 2),
('2024-11-03', 68000, 2, 3),
('2024-11-18', 45000, 2, 4),
('2024-12-02', 85000, 2, 1),
('2024-12-20', 75000, 2, 6),
('2025-01-08', 28000, 2, 5),
('2025-01-22', 32000, 2, 2);

INSERT INTO gasto (descripcion, categoria, fecha, monto, presupuesto, id_emprendedor) VALUES
('Luz y agua octubre', 'Servicios', '2024-10-01', 180000,  200000,  1),
('Insumos noviembre',  'Insumos',   '2024-11-01', 350000,  400000,  1),
('Salario diciembre',  'Nómina',    '2024-12-01', 1800000, 1800000, 1);

INSERT INTO compras_menores (descripcion, fecha, monto, id_emprendedor, id_categoria) VALUES
('Fresas 2kg',       '2024-10-10', 18000, 1, 4),
('Harina 5kg',       '2024-11-05', 22000, 1, 2),
('Chocolate amargo', '2024-12-03', 35000, 1, 5);
