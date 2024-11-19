<?php

namespace App\Models\Site;

use App\Core\Model;

class Produto extends Model
{
    public function produtoSingle($slug)
    {
        $produtoSlug = $slug;
        $query = "SELECT produtos.*, categorias.nome AS categoria_nome FROM produtos
                JOIN categorias ON produtos.categoria_id = categorias.id
                WHERE produtos.slug = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$produtoSlug]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function produtosPromocionais()
    {
        // Consulta para obter kits em promoção
        $query = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome, 
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.promocao, 
            produtos.data_lancamento, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE produtos.promocao = 1
        ORDER BY produtos.data_lancamento ASC
        LIMIT 6";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function produtosDestaques()
    {
        // Consulta para obter kits em promoção
        $query = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome,
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.promocao, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE produtos.destaque = 1
        ORDER BY produtos.data_lancamento ASC
        LIMIT 6";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function produtosUltimosLancados()
    {
        // Consulta para obter kits em promoção
        $query = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome,
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.promocao, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        ORDER BY data_lancamento ASC
        LIMIT 6";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function cosmeticosAll($search = '', $order = 'mais-relevantes', $limit = 10, $offset = 0)
    {
        // Obtendo o ID da categoria 'Cosméticos'
        $queryCategoria = "SELECT id FROM categorias WHERE nome = 'Cosméticos'";
        $stmtCategoria = $this->pdo->query($queryCategoria);
        $categoria = $stmtCategoria->fetch(\PDO::FETCH_ASSOC);
        $categoriaIdCosmeticos = $categoria['id'];

        $sql = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome,
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.promocao, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE produtos.categoria_id = :categoria_id"; // Usar :categoria_id como um parâmetro nomeado

        $params = [':categoria_id' => $categoriaIdCosmeticos];

        if ($search) {
            $sql .= " AND (produtos.nome LIKE :search OR produtos.slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Ordem Condicional
        switch ($order) {
            case 'menor-preco':
                $sql .= " ORDER BY produtos.preco ASC";
                break;
            case 'maior-preco':
                $sql .= " ORDER BY produtos.preco DESC";
                break;
            default:
                $sql .= " ORDER BY produtos.destaque ASC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countCosmeticos($search = '')
    {
        // Obtendo o ID da categoria 'Cosméticos'
        $queryCategoria = "SELECT id FROM categorias WHERE nome = 'Cosméticos'";
        $stmtCategoria = $this->pdo->query($queryCategoria);
        $categoria = $stmtCategoria->fetch(\PDO::FETCH_ASSOC);
        $categoriaIdCosmeticos = $categoria['id'];

        $sql = "SELECT COUNT(*) FROM produtos WHERE categoria_id = :categoria_id";
        $params = [
            ':categoria_id' => $categoriaIdCosmeticos
        ];

        if ($search) {
            $sql .= " AND (nome LIKE :search OR slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function perfumariasAll($search = '', $order = 'mais-relevantes', $limit = 10, $offset = 0)
    {
        // Obtendo o ID da categoria 'Cosméticos'
        $queryCategoria = "SELECT id FROM categorias WHERE nome = 'Perfumaria'";
        $stmtCategoria = $this->pdo->query($queryCategoria);
        $categoria = $stmtCategoria->fetch(\PDO::FETCH_ASSOC);
        $categoriaIdCosmeticos = $categoria['id'];

        $sql = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome,
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.promocao, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE produtos.categoria_id = :categoria_id"; // Usar :categoria_id como um parâmetro nomeado

        $params = [':categoria_id' => $categoriaIdCosmeticos];

        if ($search) {
            $sql .= " AND (produtos.nome LIKE :search OR produtos.slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Ordem Condicional
        switch ($order) {
            case 'menor-preco':
                $sql .= " ORDER BY produtos.preco ASC";
                break;
            case 'maior-preco':
                $sql .= " ORDER BY produtos.preco DESC";
                break;
            default:
                $sql .= " ORDER BY produtos.destaque ASC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPerfumarias($search = '')
    {
        // Obtendo o ID da categoria 'Cosméticos'
        $queryCategoria = "SELECT id FROM categorias WHERE nome = 'Perfumaria'";
        $stmtCategoria = $this->pdo->query($queryCategoria);
        $categoria = $stmtCategoria->fetch(\PDO::FETCH_ASSOC);
        $categoriaIdCosmeticos = $categoria['id'];

        $sql = "SELECT COUNT(*) FROM produtos WHERE categoria_id = :categoria_id";
        $params = [
            ':categoria_id' => $categoriaIdCosmeticos
        ];

        if ($search) {
            $sql .= " AND (nome LIKE :search OR slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function promocoesAll($search = '', $order = 'mais-relevantes', $limit = 10, $offset = 0)
    {
        $sql = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome,
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.promocao, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE promocao = 1";

        if ($search) {
            $sql .= " AND (produtos.nome LIKE :search OR produtos.slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Ordem Condicional
        switch ($order) {
            case 'menor-preco':
                $sql .= " ORDER BY produtos.preco ASC";
                break;
            case 'maior-preco':
                $sql .= " ORDER BY produtos.preco DESC";
                break;
            default:
                $sql .= " ORDER BY produtos.destaque ASC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countPromocoes($search = '')
    {
        $sql = "SELECT COUNT(*) FROM produtos WHERE promocao = 1";
        $params = [];  // Inicializa a array de parâmetros

        if ($search) {
            $sql .= " AND (nome LIKE :search OR slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros, se existirem
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function destaquesAll($search = '', $order = 'mais-relevantes', $limit = 10, $offset = 0)
    {
        $sql = "SELECT produtos.id as produto_id,
            produtos.nome as produto_nome,
            produtos.descricao, 
            produtos.slug, 
            produtos.preco, 
            produtos.preco_promocional, 
            produtos.codigo_barras, 
            produtos.estoque, 
            produtos.destaque, 
            produtos.promocao, 
            produtos.imagem, 
            categorias.nome as categoria_nome 
        FROM produtos
        INNER JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE destaque = 1";

        if ($search) {
            $sql .= " AND (produtos.nome LIKE :search OR produtos.slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Ordem Condicional
        switch ($order) {
            case 'menor-preco':
                $sql .= " ORDER BY produtos.preco ASC";
                break;
            case 'maior-preco':
                $sql .= " ORDER BY produtos.preco DESC";
                break;
            default:
                $sql .= " ORDER BY produtos.destaque ASC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();

        // Obtém os resultados
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countDestaques($search = '')
    {
        $sql = "SELECT COUNT(*) FROM produtos WHERE destaque = 1";
        $params = [];  // Inicializa a array de parâmetros

        if ($search) {
            $sql .= " AND (nome LIKE :search OR slug LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros, se existirem
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function detalhesProduto($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produtos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
