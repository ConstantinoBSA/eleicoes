<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Usuario extends Model
{
    private $table_name = "usuarios";
    protected $tableName = 'usuarios';

    public $id;
    public $name;
    public $email;
    public $status;
    public $perfis;

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

    // Retorna todas as usuarios, com suporte a busca, limite e offset
    public function getAll($search = '', $limit = 10, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM " . $this->table_name;
            $params = [];

            if ($search) {
                $sql .= " WHERE name LIKE :name OR email LIKE :email";
                $params[':name'] = '%' . $search . '%';
                $params[':email'] = '%' . $search . '%';
            }

            $sql .= " ORDER BY name ASC";
            $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, \PDO::PARAM_STR);
            }

            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rows as $key => &$row) {
                $rows[$key]['perfis'] = $this->getRolesByUser($row['id']);
            }

            // Transformar cada linha em um objeto Usuario
            return array_map([$this, 'mapRowToModel'], $rows);
        } catch (PDOException $e) {
            // Log the error message
            error_log($e->getMessage());
            return [];
        }
    }

    public function getRolesByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT nome FROM perfis 
            JOIN perfil_usuario ON perfis.id = perfil_usuario.perfil_id
            WHERE perfil_usuario.usuario_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Mapeia a linha do banco de dados para o objeto Usuario
    protected function mapRowToModel(array $row)
    {
        $model = new self();
        $model->id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $model->name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $model->email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
        $model->status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
        $model->perfis = $row['perfis'] ?? [];
        return $model;
    }

    // Retorna a contagem de usuarios
    public function countUsuarios($search = '')
    {
        try {
            $sql = "SELECT COUNT(*) FROM " . $this->table_name;
            $params = [];

            if ($search) {
                $sql .= " WHERE name LIKE :name OR email LIKE :email";
                $params[':name'] = '%' . $search . '%';
                $params[':email'] = '%' . $search . '%';
            }

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Log the error message
            error_log($e->getMessage());
            return 0;
        }
    }

    // Retorna a contagem de usuarios
    public function getPerfis()
    {
        try {
            $sql = "SELECT id, nome, label FROM perfis";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log the error message
            error_log($e->getMessage());
            return 0;
        }
    }

    // Busca uma perfil por ID
    public function getById($id)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table_name . ' WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $row['perfis'] = $this->getRolesByUser($row['id']);

            return $row ? $this->mapRowToModel($row) : null;
        } catch (PDOException $e) {
            // Log the error message
            error_log($e->getMessage());
            return null;
        }
    }

    // Cria uma nova perfil
    // public function create($name, $email, $status)
    // {
    //     try {
    //         $password = password_hash(generateSixDigitPassword(), PASSWORD_BCRYPT);

    //         $stmt = $this->pdo->prepare('INSERT INTO ' . $this->table_name . ' (name, email, password status) VALUES (?, ?, ?, ?)');
    //         return $stmt->execute([$name, $email, $password, $status]);
    //     } catch (PDOException $e) {
    //         // Log the error message
    //         error_log($e->getMessage());
    //         return false;
    //     }
    // }

    // // Atualiza uma perfil existente
    // public function update($id, $name, $email, $status)
    // {
    //     try {
    //         $stmt = $this->pdo->prepare('UPDATE ' . $this->table_name . ' SET name = ?, email = ?, status = ? WHERE id = ?');
    //         return $stmt->execute([$name, $email, $status, $id]);
    //     } catch (PDOException $e) {
    //         // Log the error message
    //         error_log($e->getMessage());
    //         return false;
    //     }
    // }

    // Deleta uma perfil
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM ' . $this->table_name . ' WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Log the error message
            error_log($e->getMessage());
            return false;
        }
    }
}
