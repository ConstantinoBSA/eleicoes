<?php

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    public function getAll($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM clientes";
        $params = [];
    
        if ($search) {
            $sql .= " WHERE nome LIKE :search OR slug LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY nome ASC";
    
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;
    
        $stmt = $this->pdo->prepare($sql);
    
        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
    
        $stmt->execute();
        $clientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Sanitiza os dados
        return array_map(function($cliente) {
            return [
                'id' => htmlspecialchars($cliente['id'], ENT_QUOTES, 'UTF-8'),
                'nome' => htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'),
                'telefone' => htmlspecialchars($cliente['telefone'], ENT_QUOTES, 'UTF-8'),
                'email' => htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'),
                'status' => htmlspecialchars($cliente['status'], ENT_QUOTES, 'UTF-8'),
            ];
        }, $clientes);
    }

    public function countClientes($search = '')
    {
        $sql = "SELECT COUNT(*) FROM clientes";
        $params = [];

        if ($search) {
            $sql .= " WHERE nome LIKE :search OR slug LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        if (!empty($params)) {
            $stmt->bindValue(':search', $params[':search'], \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($nome, $slug, $status)
    {
        $stmt = $this->pdo->prepare('INSERT INTO clientes (nome, slug, status) VALUES (?, ?, ?)');
        return $stmt->execute([$nome, $slug, $status]);
    }

    public function update($id, $nome, $slug, $status)
    {
        $stmt = $this->pdo->prepare('UPDATE clientes SET nome = ?, slug = ?, status = ? WHERE id = ?');
        return $stmt->execute([$nome, $slug, $status, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM clientes WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
