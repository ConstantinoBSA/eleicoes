<?php

namespace App\Models;

use App\Core\Model;

class FormaPagamento extends Model
{
    public function getAll($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM formas_pagamento";
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
        $formas_pagamento = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Sanitiza os dados
        return array_map(function($forma_pagamento) {
            return [
                'id' => htmlspecialchars($forma_pagamento['id'], ENT_QUOTES, 'UTF-8'),
                'nome' => htmlspecialchars($forma_pagamento['nome'], ENT_QUOTES, 'UTF-8'),
                'status' => htmlspecialchars($forma_pagamento['status'], ENT_QUOTES, 'UTF-8'),
            ];
        }, $formas_pagamento);
    }

    public function countFormaPagamentos($search = '')
    {
        $sql = "SELECT COUNT(*) FROM formas_pagamento";
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
        $stmt = $this->pdo->prepare('SELECT * FROM formas_pagamento WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($nome, $slug, $status)
    {
        $stmt = $this->pdo->prepare('INSERT INTO formas_pagamento (nome, slug, status) VALUES (?, ?, ?)');
        return $stmt->execute([$nome, $slug, $status]);
    }

    public function update($id, $nome, $slug, $status)
    {
        $stmt = $this->pdo->prepare('UPDATE formas_pagamento SET nome = ?, slug = ?, status = ? WHERE id = ?');
        return $stmt->execute([$nome, $slug, $status, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM formas_pagamento WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
