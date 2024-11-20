<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Chapa;
use App\Models\Escola;

class ChapaController extends Controller
{
    private $chapaModel;
    private $escolaModel;

    public function __construct()
    {
        parent::__construct();
        $this->chapaModel = new Chapa();
        $this->escolaModel = new Escola();

        if (!$this->hasPermission('chapas')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 20;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $chapas = $this->chapaModel
                ->where('nome', 'LIKE', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/chapas/index', [
                'chapas' => $chapas,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter chapas.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];

        unset($_SESSION['errors']);

        $chapa = new \stdClass();

        if (!empty($oldData)) {
            foreach ($oldData as $key => $value) {
                $chapa->$key = $value;
            }
        }

        $escolas = $this->escolaModel->where('status', true)->get();

        $this->view('admin/chapas/create', [
            'escolas' => $escolas,
            'errors' => $errors
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/chapas/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|uniqueCombination:chapas,escola_id',
                'escola_id' => 'required,',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                storeOldInput($sanitizedData);
                $this->redirect('/admin/chapas/adicionar');
            } else {    
                $chapa = $this->chapaModel->create([
                    'nome' => $sanitizedData['nome'],
                    'escola_id' => $sanitizedData['escola_id'],
                    'status' => $sanitizedData['status']
                ]);
                if ($chapa) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar chapa. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);
                clearOldInput();

                header('Location: /admin/chapas/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $chapa = $this->chapaModel->find($id);
            if (!$chapa) {
                throw new \Exception('Chapa não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];

            unset($_SESSION['errors']);
            unset($_SESSION['old']);

            if (!empty($oldData)) {
                foreach ($oldData as $key => $value) {
                    if (property_exists($chapa, $key)) {
                        $chapa->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    }
                }
            }

            // foreach ($oldData as $key => $value) {
                
            // }

            $escolas = $this->escolaModel->where('status', true)->get();

            $this->view('admin/chapas/edit', [
                'chapa' => $chapa,
                'escolas' => $escolas,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/chapas/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|uniqueCombination:chapas,escola_id,nome,'.$id,
                'escola_id' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $dadosRequisicao;
                $this->redirect('/admin/chapas/editar/' . $id);
            } else {
                $chapa = $this->chapaModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'escola_id' => $sanitizedData['escola_id'],
                    'status' => $sanitizedData['status']
                ]);

                if ($chapa) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar chapa. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/chapas/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $chapa = $this->chapaModel->find($id);
            if ($chapa) {
                $this->view('admin/chapas/show', ['chapa' => $chapa]);
            } else {
                $this->renderErrorPage('Erro 404', 'Chapa não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $chapa = $this->chapaModel->delete($id);
        if ($chapa) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/chapas/index');
    }

    public function status($id)
    {
        try {
            $reg = $this->chapaModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $chapa = $this->chapaModel->update($id, [
                'status' => $newStatus
            ]);

            if ($chapa) {
                $this->redirectToWithMessage('/admin/chapas/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/chapas/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
            }
        } catch (\Exception $e) {
            $this->handleException($e, 'Erro ao alterar status.');
        }
    }

    // Função para renderizar a view de erro
    private function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include '../resources/views/error.php';
        exit();
    }

    private function sanitizeData($data)
    {
        return [
            'nome' => $this->sanitizer->sanitizeString($_POST['nome']),
            'escola_id' => $this->sanitizer->sanitizeInt($_POST['escola_id']),
            'status' => $this->sanitizer->sanitizeString($_POST['status']),
        ];
    }
}
