<?php
require_once __DIR__ . '/../../config.php';


class Database
{
    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $db = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = $db->query("SHOW DATABASES LIKE '" . DB_NAME . "'");

        if ($query->rowCount() == 0) {
            self::createDatabase($db);
        } else {
            $db->exec("USE `" . DB_NAME . "`");
        }

        self::$connection = $db;
        return self::$connection;
    }

    /**
     * Crea la base de datos, las tablas y carga datos iniciales de prueba.
     *
     * Modelo de datos:
     * - cliente: entidad de negocio (no forma parte de la relación 1-N pedida).
     * - plan_de_cuotas: CATEGORÍA (lado 1 de la relación).
     * - cuota: ÍTEM (lado N de la relación). Cada cuota pertenece a un plan
     *   (categoría) y a un cliente.
     * - usuario: administradores del sitio.
     */
    private static function createDatabase(PDO $db)
    {
        $db->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET " . DB_CHARSET);
        $db->exec("USE `" . DB_NAME . "`");

        $sql = <<<SQL
        CREATE TABLE `cliente` (
          `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
          `nombre` varchar(100) NOT NULL,
          `email` varchar(150) NOT NULL,
          `imagen_url` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id_cliente`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE `plan_de_cuotas` (
          `id_plan` int(11) NOT NULL AUTO_INCREMENT,
          `nombre` varchar(100) NOT NULL,
          `descripcion` varchar(255) NOT NULL,
          `monto_total` decimal(10,2) NOT NULL,
          `imagen_url` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id_plan`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE `cuota` (
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

        CREATE TABLE `usuario` (
          `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(50) NOT NULL,
          `password` varchar(255) NOT NULL,
          PRIMARY KEY (`id_usuario`),
          UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            $db->exec($statement);
        }

        self::seedInitialData($db);
    }

    private static function seedInitialData(PDO $db)
    {
        // Administrador requerido por la consigna (usuario: webadmin / clave: admin)
        $hash = password_hash('admin', PASSWORD_ARGON2ID);
        $db->prepare("INSERT INTO usuario (username, password) VALUES (?, ?)")
           ->execute(['webadmin', $hash]);

        // Categorías (planes de cuotas) de prueba
        $db->exec("INSERT INTO plan_de_cuotas (nombre, descripcion, monto_total, imagen_url) VALUES
            ('Plan Viajes 12 cuotas', 'Financiación para paquetes turísticos en 12 cuotas fijas.', 180000.00, 'https://cdn-icons-png.flaticon.com/512/3104/3104613.png'),
            ('Plan Hogar 6 cuotas', 'Financiación para electrodomésticos y muebles en 6 cuotas.', 90000.00, 'https://cdn-icons-png.flaticon.com/512/2933/2933116.png'),
            ('Plan Tecnología 3 cuotas', 'Financiación corta para equipos tecnológicos.', 60000.00, 'https://cdn-icons-png.flaticon.com/512/3659/3659898.png')
        ");

        // Clientes de prueba
        $db->exec("INSERT INTO cliente (nombre, email, imagen_url) VALUES
            ('Juan Perez', 'juan@gmail.com', 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'),
            ('Maria Gomez', 'maria@gmail.com', 'https://cdn-icons-png.flaticon.com/512/3135/3135768.png')
        ");

        // Cuotas de prueba (ítems), vinculadas a un plan (categoría) y a un cliente
        $db->exec("INSERT INTO cuota (id_plan, id_cliente, nro_cuota, monto, estado_pago, imagen_url) VALUES
            (1, 1, 1, 15000.00, 1, NULL),
            (1, 1, 2, 15000.00, 0, NULL),
            (2, 2, 1, 15000.00, 1, NULL),
            (3, 2, 1, 20000.00, 0, NULL)
        ");
    }
}
