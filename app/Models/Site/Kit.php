<?php

namespace App\Models\Site;

use App\Core\Model;

class Kit extends Model
{
    public function kitsPromocionais()
    {
        $sql = "SELECT kits.nome, 
            kits.descricao, 
            kits.slug, 
            kits.preco,
            kits.imagem
        FROM kits";

        $sql .= " ORDER BY nome ASC";
    
        $sql .= " LIMIT 6";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function kitsAll($search = '', $order = 'mais-relevantes', $limit = 10, $offset = 0)
    {
        $sql = "SELECT kits.nome, 
            kits.descricao, 
            kits.slug, 
            kits.preco,
            kits.imagem
        FROM kits";
        $params = [];

        if ($search) {
            $sql .= " WHERE nome LIKE :search OR slug LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        // Ordem Condicional
        switch ($order) {
            case 'menor-preco':
                $sql .= " ORDER BY preco ASC";
                break;
            case 'maior-preco':
                $sql .= " ORDER BY preco DESC";
                break;
            default:
                $sql .= " ORDER BY destaque ASC";
        }
    
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;
    
        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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

    public function kitDetalhes($slug)
    {
        $kitSlug = $slug;
        $query = "SELECT kits.* FROM kits
                WHERE kits.slug = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$kitSlug]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function produtosKit($kitId)
    {
        // Consulta para obter todos os produtos associados a um kit específico
        $query = "SELECT produtos.*,
            categorias.id AS categoria_id,
            categorias.nome AS categoria_nome
            FROM produtos
            INNER JOIN kits_produtos ON kits_produtos.produto_id = produtos.id
            INNER JOIN categorias ON produtos.categoria_id = categorias.id
            WHERE kits_produtos.kit_id = ?";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$kitId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
