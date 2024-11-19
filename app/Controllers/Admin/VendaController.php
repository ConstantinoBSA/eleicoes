<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\Produto;
use App\Models\Venda;

class VendaController extends Controller
{
    protected $sanitizer;
    protected $validator;

    public function __construct()
    {
        $this->sanitizer = new Sanitizer();
        $this->validator = new Validator();
        $this->auditLogger = new AuditLogger();
    }

    public function index()
    {
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 10; // Número de vendas por página
        $offset = ($page - 1) * $limit;
        
        $vendaModel = new Venda();
        $vendas = $vendaModel->getAll($search, $limit, $offset);
        $totalVendas = $vendaModel->countVendas($search);
        $totalPages = ceil($totalVendas / $limit);

        $start = $offset + 1;
        $end = min($offset + $limit, $totalVendas);

        $this->view('admin/vendas/index', [
            'vendas' => $vendas,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalVendas' => $totalVendas,
            'start' => $start,
            'end' => $end,
            'search' => $search
        ]);
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }            

            $_SESSION['venda_items'] = $_POST['items'];

            // $items = array_map(function($item) {
            //     return [
            //         'produto_id' => (int) $item['produto_id'],
            //         'quantidade' => (int) $item['quantidade'],
            //         'preco' => (float) str_replace(['.', ','], ['', '.'], $item['preco'])
            //     ];
            // }, $sanitizedData['items'] ?? []);

            $sanitizedData = [
                'cliente_id' => $this->sanitizer->sanitizeInt($_POST['cliente_id']),
                'forma_pagamento_id' => $this->sanitizer->sanitizeInt($_POST['forma_pagamento_id'])
            ];  
    
            // $rules = [
            //     'cliente_id' => 'required',
            //     'forma_pagamento_id' => 'required',
            //     'items' => 'required',
            // ];

            // $errors = $this->validator->validate($sanitizedData, $rules);

            // if (!empty($errors)) {
                
                
                // $this->view('admin/vendas/create', ['error' => $errors, 'data' => $sanitizedData]);
            // } else {    
                $vendaModel = new Venda();
                $vendaId = $vendaModel->createVenda($sanitizedData['cliente_id'], $sanitizedData['forma_pagamento_id'], $_POST['items']);
                if ($vendaId) {
                    foreach ($_POST['items'] as $item) {
                        $itemModel = new Venda();
                        $itemModel->createItemVenda($vendaId, (int) $item['produto_id'], (int) $item['quantidade'], (float) $item['preco']);
                    }

                    unset($_SESSION['venda_items']);
        
                    $_SESSION['message'] = "Venda registrada com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao registrar a venda. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "danger";
                }
        
                unset($_SESSION['csrf_token']);
                header('Location: /admin/vendas/index');
                exit();
            // }
        } else {
            $clienteModel = new Cliente();
            $formaPagamentoModel = new FormaPagamento();
            $produtoModel = new Produto();
            $clientes = $clienteModel->getAll();
            $formasPagamento = $formaPagamentoModel->getAll();
            $produtos = $produtoModel->getProdutosVenda();
    
            $this->view('admin/vendas/create', [
                'clientes' => $clientes,
                'formasPagamento' => $formasPagamento,
                'produtos' => $produtos,
                'data' => [],
                'error' => []
            ]);
        }
    }

    public function edit($id)
    {
        $vendaModel = new Venda();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $rules = [
                'nome' => 'required|unique:vendas,nome,'.$id,
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($_POST, $rules);

            if (!empty($errors)) {
                $data = array_merge($_POST, ['id' => $id]);
                $this->view('admin/vendas/edit', ['error' => $errors, 'data' => $data]);
            } else {
                $venda = $vendaModel->update($id, $_POST['nome'], $_POST['slug'], $_POST['status']);
                if ($venda) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar venda. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/vendas/index');
            }
        } else {
            $venda = $vendaModel->getById($id);
            if ($venda) {
                $this->view('admin/vendas/edit', ['error' => [], 'data' => $venda]);
            } else {
                $this->renderErrorPage('Erro 404', 'Venda não encontrada.');
            }
        }
    }

    public function show($id)
    {
        $vendaModel = new Venda();
        $venda = $vendaModel->getById($id);
        if ($venda) {
            $this->view('admin/vendas/show', ['venda' => $venda]);
        } else {
            $this->renderErrorPage('Erro 404', 'Venda não encontrada.');
        }
    }

    public function delete($id)
    {
        $vendaModel = new Venda();
        $venda = $vendaModel->delete($id);
        if ($venda) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/vendas/index');
    }

    // Função para renderizar a view de erro
    private function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include '../resources/views/error.php';
        exit();
    }
}
