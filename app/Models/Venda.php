<?php

namespace App\Models;

use App\Core\Model;

class Venda extends Model
{
    protected $tableName = 'vendas';

    public function getAll($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT 
            vendas.*,
            clientes.nome AS cliente_nome,
            formas_pagamento.nome AS forma_pagamento_nome 
        FROM vendas
        JOIN clientes ON vendas.cliente_id = clientes.id
        JOIN formas_pagamento ON vendas.forma_pagamento_id = formas_pagamento.id";
        $params = [];

        if ($search) {
            $sql .= " WHERE vendas.numero LIKE :search OR clientes.nome LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY vendas.data_venda DESC";
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;

        $stmt = $this->pdo->prepare($sql);

        // Vincula os parâmetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();
        $vendas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Sanitiza os dados
        return array_map(function($venda) {
            return [
                'id' => htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'),
                'cliente_nome' => htmlspecialchars($venda['cliente_nome'], ENT_QUOTES, 'UTF-8'),
                'forma_pagamento_nome' => htmlspecialchars($venda['forma_pagamento_nome'], ENT_QUOTES, 'UTF-8'),
                'numero' => htmlspecialchars($venda['numero'], ENT_QUOTES, 'UTF-8'),
                'data_venda' => htmlspecialchars($venda['data_venda'], ENT_QUOTES, 'UTF-8'),
                'total' => htmlspecialchars($venda['total'], ENT_QUOTES, 'UTF-8'),
                'status' => htmlspecialchars($venda['status'], ENT_QUOTES, 'UTF-8'),
            ];
        }, $vendas);
    }

    public function countVendas($search = '')
    {
        $sql = "SELECT COUNT(*) FROM vendas";
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
        $stmt = $this->pdo->prepare('SELECT * FROM vendas WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function createVenda($cliente_id, $forma_pagamento_id, $itens)
    {
        // Gerar número da venda
        $numeroVenda = generateSaleNumber(); // Implemente este método para gerar um número de venda único
        $dataVenda = date('Y-m-d H:i:s');        

        // Calcular valor total da venda
        $total = array_reduce($itens, function($sum, $item) {
            $preco = str_replace(['.', ','], ['', '.'], (float) $item['preco']);
            return $sum + ($item['quantidade'] * $preco);
        }, 0);

        // Inserir a venda
        $stmt = $this->pdo->prepare('INSERT INTO vendas (cliente_id, forma_pagamento_id, numero, data_venda, total) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$cliente_id, $forma_pagamento_id, $numeroVenda, $dataVenda, $total]);

        return $this->pdo->lastInsertId(); // Retorna o ID da venda inserida
    }

    public function createItemVenda($vendaId, $produtoId, $quantidade, $preco)
    {
        $preco = str_replace(['.', ','], ['', '.'], (float) $preco);
        $stmt = $this->pdo->prepare('INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$vendaId, $produtoId, $quantidade, str_replace(['.', ','], ['', '.'], $preco)]);
    }

    // public function update($id, $nome, $slug, $status)
    // {
    //     $stmt = $this->pdo->prepare('UPDATE vendas SET nome = ?, slug = ?, status = ? WHERE id = ?');
    //     return $stmt->execute([$nome, $slug, $status, $id]);
    // }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM vendas WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function obterVendasPorPeriodo($dataInicial, $dataFinal)
    {
        // Substitua por sua lógica para buscar dados do banco
        $query = "SELECT v.id, c.nome as cliente_nome, v.data_venda, v.total 
                  FROM vendas v 
                  JOIN clientes c ON v.cliente_id = c.id 
                  WHERE v.data_venda BETWEEN :dataInicial AND :dataFinal 
                  ORDER BY v.data_venda";

        // Preparar e executar a consulta, substituindo os placeholders
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':dataInicial', $dataInicial);
        $stmt->bindParam(':dataFinal', $dataFinal);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
