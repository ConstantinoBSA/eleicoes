<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Pdv;

class PdvController extends Controller
{
    public function __construct()
    {
        if (!$this->hasPermission('pdv')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }
    
    public function index()
    {
        $this->view('admin/pdv/index');
    }

    public function tela()
    {
        $this->view('admin/pdv/tela');
    }

    public function search_products()
    {
        // Captura da query
        $query = $_GET['query'] ?? '';

        $pdo = Database::getInstance()->getConnection();

        // Busca de produtos
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE :queryNome OR codigo_barras LIKE :queryCodigo");
        $queryParam = "%$query%";
        $stmt->execute([':queryNome' => $queryParam, ':queryCodigo' => $queryParam]);

        // Retorno dos dados
        $produtos = $stmt->fetchAll();

        foreach ($produtos as $produto) {
            echo '<li class="search-item" data-item="'.htmlspecialchars(json_encode($produto), ENT_QUOTES, 'UTF-8').'">
                    <img src="../../images/imagem-300x200.jpg" alt="'.$produto['nome'].'" style="width: 50px; height: auto;">
                    '.$produto['nome'].' - R$'.$produto['preco'].'
                    <button class="add-to-cart btn btn-sm btn-primary">Adicionar</button>
                </li>';
        }
    }

    public function addProduct()
    {
        $pdv = new Pdv();
        $productData = json_decode($_POST['product'], true);
        $pdv->addProductToCart($productData);
        echo json_encode($pdv->getCart());
    }

    public function updateQuantity()
    {
        $pdv = new Pdv();
        $codigoBarras = $_POST['codigo_barras'];
        $quantidade = (int)$_POST['quantidade'];
        $pdv->updateProductQuantity($codigoBarras, $quantidade);
        echo json_encode($pdv->getCart());
    }

    public function removeProduct()
    {
        session_start();
        $pdv = new Pdv();
        $codigoBarras = $_POST['codigo_barras'];
        $pdv->removeProductFromCart($codigoBarras);
        echo json_encode($pdv->getCart());
    }

    public function clearCart()
    {
        $pdv = new Pdv();
        $pdv->clearCart();
        echo json_encode($pdv->getCart());
    }

    public function finalizePurchase()
    {
        $pdv = new Pdv();
        $pdv->finalizePurchase();
        echo json_encode($pdv->getCart());
    }

    public function getCart()
    {
        $pdv = new Pdv();
        echo json_encode($pdv->getCart());
    }
}
