<?php

namespace App\Core;

class AuditLogger
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function log($userId, $action, $details = null)
    {
        $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (:user_id, :action, :details)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, \PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, \PDO::PARAM_STR);
        $stmt->execute();
    }
}
