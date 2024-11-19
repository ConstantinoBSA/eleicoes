<?php

namespace App\Core;

require_once __DIR__ . '/../../config/constants.php';

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_DATABASE.';charset=utf8mb4';
        $user = DB_USER;
        $password = DB_PASS;

        try {
            $this->pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
            exit;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    // Proíbe clonagem e desserialização da instância do singleton
    // private function __clone() {}
    // private function __wakeup() {}
}
