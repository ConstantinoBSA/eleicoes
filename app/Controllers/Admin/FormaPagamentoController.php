<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\FormaPagamento;

class FormaPagamentoController extends Controller
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
        $limit = 10; // Número de formas_pagamento por página
        $offset = ($page - 1) * $limit;
        
        $formaPagamentoModel = new FormaPagamento();
        $formas_pagamento = $formaPagamentoModel->getAll($search, $limit, $offset);
        $totalFormaPagamentos = $formaPagamentoModel->countFormaPagamentos($search);
        $totalPages = ceil($totalFormaPagamentos / $limit);

        $start = $offset + 1;
        $end = min($offset + $limit, $totalFormaPagamentos);

        $this->view('admin/formas_pagamento/index', [
            'formas_pagamento' => $formas_pagamento,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalFormaPagamentos' => $totalFormaPagamentos,
            'start' => $start,
            'end' => $end,
            'search' => $search
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $sanitizedData = [
                'nome' => $this->sanitizer->sanitizeString($_POST['nome']),
                'slug' => $this->sanitizer->sanitizeString($_POST['slug']),
                'status' => $this->sanitizer->sanitizeString($_POST['status']),
            ];

            $rules = [
                'nome' => 'required|unique:formas_pagamento',
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $this->view('admin/formas_pagamento/create', ['error' => $errors, 'data' => $sanitizedData]);
            } else {
                $formaPagamentoModel = new FormaPagamento();
                $forma_pagamento = $formaPagamentoModel->create($sanitizedData['nome'], $sanitizedData['slug'], $sanitizedData['status']);
                if ($forma_pagamento) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar forma_pagamento. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/formas_pagamento/index');
            }
        } else {
            $this->view('admin/formas_pagamento/create', ['error' => [], 'data' => []]);
        }
    }

    public function edit($id)
    {
        $formaPagamentoModel = new FormaPagamento();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $rules = [
                'nome' => 'required|unique:formas_pagamento,nome,'.$id,
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($_POST, $rules);

            if (!empty($errors)) {
                $data = array_merge($_POST, ['id' => $id]);
                $this->view('admin/formas_pagamento/edit', ['error' => $errors, 'data' => $data]);
            } else {
                $forma_pagamento = $formaPagamentoModel->update($id, $_POST['nome'], $_POST['slug'], $_POST['status']);
                if ($forma_pagamento) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar forma_pagamento. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/formas_pagamento/index');
            }
        } else {
            $forma_pagamento = $formaPagamentoModel->getById($id);
            if ($forma_pagamento) {
                $this->view('admin/formas_pagamento/edit', ['error' => [], 'data' => $forma_pagamento]);
            } else {
                $this->renderErrorPage('Erro 404', 'FormaPagamento não encontrada.');
            }
        }
    }

    public function show($id)
    {
        $formaPagamentoModel = new FormaPagamento();
        $forma_pagamento = $formaPagamentoModel->getById($id);
        if ($forma_pagamento) {
            $this->view('admin/formas_pagamento/show', ['forma_pagamento' => $forma_pagamento]);
        } else {
            $this->renderErrorPage('Erro 404', 'FormaPagamento não encontrada.');
        }
    }

    public function delete($id)
    {
        $formaPagamentoModel = new FormaPagamento();
        $forma_pagamento = $formaPagamentoModel->delete($id);
        if ($forma_pagamento) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/formas_pagamento/index');
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
