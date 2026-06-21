-- Base de datos compartida con el TP Parte 2.
-- Se incluye completa (tal cual la entrega anterior) + la tabla nueva
-- `api_token`, necesaria para la autenticación opcional de la API REST.
-- IMPORTANTE: no se modificó ninguna tabla existente, solo se agregó una nueva,
-- para no romper la entrega anterior.

CREATE DATABASE IF NOT EXISTS `db_planes_cuotas` DEFAULT CHARACTER SET utf8mb4;
USE `db_planes_cuotas`;

CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `plan_de_cuotas` (
  `id_plan` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cuota` (
  `id_cuota` int(11) NOT NULL AUTO_INCREMENT,
  `id_plan` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `nro_cuota` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado_pago` tinyint(1) NOT NULL DEFAULT 0,
  `imagen_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cuota`),
  KEY `id_plan` (`id_plan`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `cuota_plan_fk` FOREIGN KEY (`id_plan`) REFERENCES `plan_de_cuotas` (`id_plan`) ON DELETE CASCADE,
  CONSTRAINT `cuota_cliente_fk` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla nueva: tokens válidos para consumir la API REST (requerimiento
-- opcional de autenticación en POST/PUT).
CREATE TABLE IF NOT EXISTS `api_token` (
  `id_token` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de prueba (no se insertan si ya existen, ver INSERT IGNORE)
INSERT IGNORE INTO usuario (id_usuario, username, password) VALUES
  (1, 'webadmin', '$argon2id$v=19$m=65536,t=4,p=1$c2FsdHNhbHRzYWx0$ZpJtZ0vWv8m1y7l8b6oZxw');

INSERT IGNORE INTO plan_de_cuotas (id_plan, nombre, descripcion, monto_total, imagen_url) VALUES
  (1, 'Plan Viajes 12 cuotas', 'Financiación para paquetes turísticos en 12 cuotas fijas.', 180000.00, 'https://cdn-icons-png.flaticon.com/512/3104/3104613.png'),
  (2, 'Plan Hogar 6 cuotas', 'Financiación para electrodomésticos y muebles en 6 cuotas.', 90000.00, 'https://cdn-icons-png.flaticon.com/512/2933/2933116.png'),
  (3, 'Plan Tecnología 3 cuotas', 'Financiación corta para equipos tecnológicos.', 60000.00, 'https://cdn-icons-png.flaticon.com/512/3659/3659898.png');

INSERT IGNORE INTO cliente (id_cliente, nombre, email, imagen_url) VALUES
  (1, 'Juan Perez', 'juan@gmail.com', 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'),
  (2, 'Maria Gomez', 'maria@gmail.com', 'https://cdn-icons-png.flaticon.com/512/3135/3135768.png');

INSERT IGNORE INTO cuota (id_cuota, id_plan, id_cliente, nro_cuota, monto, estado_pago, imagen_url) VALUES
  (1, 1, 1, 1, 15000.00, 1, NULL),
  (2, 1, 1, 2, 15000.00, 0, NULL),
  (3, 2, 2, 1, 15000.00, 1, NULL),
  (4, 3, 2, 1, 20000.00, 0, NULL);

INSERT IGNORE INTO api_token (id_token, token, descripcion) VALUES
  (1, 'TP2026-DEMO-TOKEN-12345', 'Token de prueba para Postman');

CREATE TABLE IF NOT EXISTS `api_token` (
  `id_token` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO api_token (id_token, token, descripcion) VALUES
  (1, 'TP2026-DEMO-TOKEN-12345', 'Token de prueba para Postman');