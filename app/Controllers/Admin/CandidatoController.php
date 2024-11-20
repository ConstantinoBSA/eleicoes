<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Candidato;
use App\Models\Escola;

class CandidatoController extends Controller
{
    private $candidatoModel;
    private $escolaModel;

    public function __construct()
    {
        parent::__construct();
        $this->candidatoModel = new Candidato();
        $this->escolaModel = new Escola();

        if (!$this->hasPermission('candidatos')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 20;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $candidatos = $this->candidatoModel
                ->where('nome', 'LIKE', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/candidatos/index', [
                'candidatos' => $candidatos,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter candidatos.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $candidato = new \stdClass();

        if (!empty($oldData)) {
            foreach ($oldData as $key => $value) {
                $candidato->$key = $value;
            }
        }

        $escolas = $this->escolaModel->where('status', true)->get();

        $this->view('admin/candidatos/create', [
            'escolas' => $escolas,
            'errors' => $errors,
            'candidato' => $candidato
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/candidatos/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:candidatos',
                'escola_id' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/candidatos/adicionar');
            } else {    
                $candidato = $this->candidatoModel->create([
                    'nome' => $sanitizedData['nome'],
                    'escola_id' => $sanitizedData['escola_id'],
                    'status' => $sanitizedData['status']
                ]);
                if ($candidato) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar candidato. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/candidatos/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $candidato = $this->candidatoModel->find($id);
            if (!$candidato) {
                throw new \Exception('Candidato não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($candidato, $key)) {
                    $candidato->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $escolas = $this->escolaModel->where('status', true)->get();

            $this->view('admin/candidatos/edit', [
                'candidato' => $candidato,
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
                $this->redirectToWithMessage('/admin/candidatos/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:candidatos,nome,'.$id,
                'escola_id' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/candidatos/editar/' . $id);
            } else {
                $candidato = $this->candidatoModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'escola_id' => $sanitizedData['escola_id'],
                    'status' => $sanitizedData['status']
                ]);

                if ($candidato) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar candidato. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/candidatos/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $candidato = $this->candidatoModel->find($id);
            if ($candidato) {
                $this->view('admin/candidatos/show', ['candidato' => $candidato]);
            } else {
                $this->renderErrorPage('Erro 404', 'Candidato não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $candidato = $this->candidatoModel->delete($id);
        if ($candidato) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/candidatos/index');
    }

    public function status($id)
    {
        try {
            $reg = $this->candidatoModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $candidato = $this->candidatoModel->update($id, [
                'status' => $newStatus
            ]);

            if ($candidato) {
                $this->redirectToWithMessage('/admin/candidatos/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/candidatos/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
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
            'escola_id' => $this->sanitizer->sanitizeString($_POST['escola_id']),
            'status' => $this->sanitizer->sanitizeString($_POST['status']),
        ];
    }
}
