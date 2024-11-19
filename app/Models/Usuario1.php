<?php

namespace App\Models;

use App\Core\Model;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Usuario extends Model
{
    public function getByEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function storeResetToken($email, $token)
    {
        // Verifica se já existe um registro para este e-mail
        $sql = "SELECT * FROM password_resets WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $existingToken = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existingToken) {
            // Atualiza o token e o timestamp se já existir um registro
            $sql = "UPDATE password_resets SET token = :token, created_at = NOW() WHERE email = :email";
        } else {
            // Insere um novo registro se não existir
            $sql = "INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function findEmailByToken($token)
    {
        $sql = "SELECT email FROM password_resets WHERE token = :token LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['email'] : false;
    }

    public function updatePassword($email, $newPassword)
    {
        $sql = "UPDATE usuarios SET password = :password WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':password', $newPassword, \PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function invalidateToken($token)
    {
        $sql = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function storeEmailVerificationToken($email, $token)
    {
        $sql = "INSERT INTO email_verifications (email, token, created_at) VALUES (:email, :token, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getAll($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM usuarios";
        $params = [];
    
        if ($search) {
            $sql .= " WHERE name LIKE :search OR email LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY name ASC";
    
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;
    
        $stmt = $this->pdo->prepare($sql);
    
        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
    
        $stmt->execute();
        $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Sanitiza os dados
        return array_map(function ($usuario) {
            return [
                'id' => htmlspecialchars($usuario['id'], ENT_QUOTES, 'UTF-8'),
                'name' => htmlspecialchars($usuario['name'], ENT_QUOTES, 'UTF-8'),
                'email' => htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'),
                'status' => htmlspecialchars($usuario['status'], ENT_QUOTES, 'UTF-8'),
            ];
        }, $usuarios);
    }

    public function countUsuarios($search = '')
    {
        $sql = "SELECT COUNT(*) FROM usuarios";
        $params = [];

        if ($search) {
            $sql .= " WHERE name LIKE :search OR email LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        if (!empty($params)) {
            $stmt->bindValue(':search', $params[':search'], \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function findEmailVerificationByToken($token)
    {
        $sql = "SELECT email FROM email_verifications WHERE token = :token LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['email'] : false;
    }

    public function verifyUserEmail($email)
    {
        $sql = "UPDATE usuarios SET email_verified_at = NOW() WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function invalidateVerificationToken($token)
    {
        $sql = "DELETE FROM email_verifications WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($name, $email, $status)
    {
        $password = password_hash(generateSixDigitPassword(), PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare('INSERT INTO usuarios (name, email, password, status) VALUES (?, ?, ?, ?)');
        // Execute a inserção
        $stmt->execute([$name, $email, $password, $status]);

        // Obtenha o ID do último registro inserido
        $lastInsertId = $this->pdo->lastInsertId();

        // Recupere o registro completo
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$lastInsertId]);

        // Retorne o registro como um array associativo
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function update($id, $email)
    {
        // Prepare a instrução de atualização
        $stmt = $this->pdo->prepare('UPDATE usuarios SET email = ? WHERE id = ?');
        
        // Execute a atualização
        $stmt->execute([$email, $id]);

        // Recupere o registro atualizado
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);

        // Retorne o registro atualizado como um array associativo
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
