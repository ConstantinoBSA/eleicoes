<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Kit;

class KitController extends Controller
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
        $limit = 10; // Número de kits por página
        $offset = ($page - 1) * $limit;
        
        $kitModel = new Kit();
        $kits = $kitModel->getAll($search, $limit, $offset);
        $totalKits = $kitModel->countKits($search);
        $totalPages = ceil($totalKits / $limit);

        $start = $offset + 1;
        $end = min($offset + $limit, $totalKits);

        $this->view('admin/kits/index', [
            'kits' => $kits,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalKits' => $totalKits,
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
                'nome' => 'required|unique:kits',
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $this->view('admin/kits/create', ['error' => $errors, 'data' => $sanitizedData]);
            } else {
                $kitModel = new Kit();
                $kit = $kitModel->create($sanitizedData['nome'], $sanitizedData['slug'], $sanitizedData['status']);
                if ($kit) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar kit. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/kits/index');
            }
        } else {
            $this->view('admin/kits/create', ['error' => [], 'data' => []]);
        }
    }

    public function edit($id)
    {
        $kitModel = new Kit();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $rules = [
                'nome' => 'required|unique:kits,nome,'.$id,
                'slug' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($_POST, $rules);

            if (!empty($errors)) {
                $data = array_merge($_POST, ['id' => $id]);
                $this->view('admin/kits/edit', ['error' => $errors, 'data' => $data]);
            } else {
                $kit = $kitModel->update($id, $_POST['nome'], $_POST['slug'], $_POST['status']);
                if ($kit) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar kit. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/kits/index');
            }
        } else {
            $kit = $kitModel->getById($id);
            if ($kit) {
                $this->view('admin/kits/edit', ['error' => [], 'data' => $kit]);
            } else {
                $this->renderErrorPage('Erro 404', 'Kit não encontrada.');
            }
        }
    }

    public function show($id)
    {
        $kitModel = new Kit();
        $kit = $kitModel->getById($id);
        if ($kit) {
            $this->view('admin/kits/show', ['kit' => $kit]);
        } else {
            $this->renderErrorPage('Erro 404', 'Kit não encontrada.');
        }
    }

    public function delete($id)
    {
        $kitModel = new Kit();
        $kit = $kitModel->delete($id);
        if ($kit) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/kits/index');
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
