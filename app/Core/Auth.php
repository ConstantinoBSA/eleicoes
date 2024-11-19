<?php

namespace App\Core;

class Auth
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function user()
    {
        if (isset($_SESSION['user_id'])) {
            return $this->getUserById($_SESSION['user_id']);
        }
        return null;
    }

    public function getUserById($userId)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ); // Retorna o usuário como um objeto
    }
}
