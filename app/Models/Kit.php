<?php

namespace App\Models;

use App\Core\Model;

class Kit extends Model
{
    protected $tableName = 'kits';

    public function getAll($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM kits";
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
        $kits = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Sanitiza os dados
        return array_map(function($kit) {
            return [
                'id' => htmlspecialchars($kit['id'], ENT_QUOTES, 'UTF-8'),
                'nome' => htmlspecialchars($kit['nome'], ENT_QUOTES, 'UTF-8'),
                'slug' => htmlspecialchars($kit['slug'], ENT_QUOTES, 'UTF-8'),
                'status' => htmlspecialchars($kit['status'], ENT_QUOTES, 'UTF-8'),
            ];
        }, $kits);
    }

    public function countKits($search = '')
    {
        $sql = "SELECT COUNT(*) FROM kits";
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
        $stmt = $this->pdo->prepare('SELECT * FROM kits WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // public function create($nome, $slug, $status)
    // {
    //     $stmt = $this->pdo->prepare('INSERT INTO kits (nome, slug, status) VALUES (?, ?, ?)');
    //     return $stmt->execute([$nome, $slug, $status]);
    // }

    // public function update($id, $nome, $slug, $status)
    // {
    //     $stmt = $this->pdo->prepare('UPDATE kits SET nome = ?, slug = ?, status = ? WHERE id = ?');
    //     return $stmt->execute([$nome, $slug, $status, $id]);
    // }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM kits WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
