<?php

namespace App\Models;

use App\Core\Model;

class AccessLog extends Model
{
    public function registerLogin()
    {
        $sql = "INSERT INTO access_logs (user_id, ip_address, user_agent, session_id) 
        VALUES (:user_id, :ip_address, :user_agent, :session_id)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => auth()->user()->id ?? '',
            ':ip_address' => $_SERVER['REMOTE_ADDR'],
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
            ':session_id' => session_id()
        ]);

        // Armazene o ID da sessão para uso posterior
        $_SESSION['access_log_id'] = $this->pdo->lastInsertId();
    }

    public function registerLogout()
    {
        if (isset($_SESSION['access_log_id'])) {
            $sql = "UPDATE access_logs SET logout_time = NOW() WHERE id = :access_log_id";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':access_log_id' => $_SESSION['access_log_id']]);
        }
    }
}
