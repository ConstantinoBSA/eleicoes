<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Cliente;

class ClienteController extends Controller
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
        $limit = 10; // Número de clientes por página
        $offset = ($page - 1) * $limit;
        
        $clienteModel = new Cliente();
        $clientes = $clienteModel->getAll($search, $limit, $offset);
        $totalClientes = $clienteModel->countClientes($search);
        $totalPages = ceil($totalClientes / $limit);

        $start = $offset + 1;
        $end = min($offset + $limit, $totalClientes);

        $this->view('admin/clientes/index', [
            'clientes' => $clientes,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalClientes' => $totalClientes,
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
                'nome' => 'required|unique:clientes',
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $this->view('admin/clientes/create', ['error' => $errors, 'data' => $sanitizedData]);
            } else {
                $clienteModel = new Cliente();
                $cliente = $clienteModel->create($sanitizedData['nome'], $sanitizedData['slug'], $sanitizedData['status']);
                if ($cliente) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar cliente. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/clientes/index');
            }
        } else {
            $this->view('admin/clientes/create', ['error' => [], 'data' => []]);
        }
    }

    public function edit($id)
    {
        $clienteModel = new Cliente();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $rules = [
                'nome' => 'required|unique:clientes,nome,'.$id,
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($_POST, $rules);

            if (!empty($errors)) {
                $data = array_merge($_POST, ['id' => $id]);
                $this->view('admin/clientes/edit', ['error' => $errors, 'data' => $data]);
            } else {
                $cliente = $clienteModel->update($id, $_POST['nome'], $_POST['slug'], $_POST['status']);
                if ($cliente) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar cliente. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/clientes/index');
            }
        } else {
            $cliente = $clienteModel->getById($id);
            if ($cliente) {
                $this->view('admin/clientes/edit', ['error' => [], 'data' => $cliente]);
            } else {
                $this->renderErrorPage('Erro 404', 'Cliente não encontrada.');
            }
        }
    }

    public function show($id)
    {
        $clienteModel = new Cliente();
        $cliente = $clienteModel->getById($id);
        if ($cliente) {
            $this->view('admin/clientes/show', ['cliente' => $cliente]);
        } else {
            $this->renderErrorPage('Erro 404', 'Cliente não encontrada.');
        }
    }

    public function delete($id)
    {
        $clienteModel = new Cliente();
        $cliente = $clienteModel->delete($id);
        if ($cliente) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/clientes/index');
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
