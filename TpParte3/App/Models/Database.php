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

        $db = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
            DB_USER,
            DB_PASS
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$connection = $db;
        return self::$connection;
    }
}
