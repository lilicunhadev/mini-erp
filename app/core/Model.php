<?php
namespace App\Core;

use PDO;
use PDOException;

abstract class Model
{
    private static ?PDO $db = null;

    protected static function db(): PDO
    {
        if (self::$db === null) {
            $config = require BASE_PATH . '/config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
            try {
                self::$db = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Falha na conexão: ' . $e->getMessage());
            }
        }
        return self::$db;
    }
}
