<?php

namespace App\Models;

use App\Core\Model;

class Pdv extends Model
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addProductToCart($productData)
    {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['codigo_barras'] === $productData['codigo_barras']) {
                $item['quantidade'] += 1;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $productData['quantidade'] = 1;
            $_SESSION['cart'][] = $productData;
        }
    }

    public function updateProductQuantity($codigoBarras, $quantidade)
    {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['codigo_barras'] === $codigoBarras) {
                $item['quantidade'] = $quantidade;
                break;
            }
        }
    }

    public function removeProductFromCart($codigoBarras)
    {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['codigo_barras'] === $codigoBarras) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexa o array
                break;
            }
        }
    }

    public function clearCart()
    {
        $_SESSION['cart'] = [];
    }

    public function finalizePurchase()
    {
        try {
            $this->pdo->beginTransaction();

            // Calcula o total da venda
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['preco'] * $item['quantidade'];
            }

            // Insere a venda em `vendas`
            $stmt = $this->pdo->prepare("INSERT INTO vendas (total) VALUES (:total)");
            $stmt->execute([':total' => $total]);
            $vendaId = $this->pdo->lastInsertId();

            // Insere cada item em `itens_venda`
            $stmt = $this->pdo->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco) VALUES (:venda_id, :produto_id, :quantidade, :preco)");
            foreach ($_SESSION['cart'] as $item) {
                $stmt->execute([
                    ':venda_id' => $vendaId,
                    ':produto_id' => $item['id'],
                    ':quantidade' => $item['quantidade'],
                    ':preco' => $item['preco']
                ]);
            }

            $this->pdo->commit();
            $_SESSION['cart'] = [];

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCart()
    {
        return $_SESSION['cart'];
    }        
}
