<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Eleitor;
use App\Models\Escola;
use App\Models\Segmento;

class EleitorController extends Controller
{
    private $eleitorModel;
    private $segmentoModel;
    private $escolaModel;

    public function __construct()
    {
        parent::__construct();
        $this->eleitorModel = new Eleitor();
        $this->segmentoModel = new Segmento();
        $this->escolaModel = new Escola();

        if (!$this->hasPermission('eleitores')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 20;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $eleitores = $this->eleitorModel
                ->where('nome', 'LIKE', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/eleitores/index', [
                'eleitores' => $eleitores,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter eleitores.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $eleitor = new \stdClass();

        if (!empty($oldData)) {
            foreach ($oldData as $key => $value) {
                $eleitor->$key = $value;
            }
        }

        $segmentos = $this->segmentoModel->where('status', true)->get();
        $escolas = $this->escolaModel->where('status', true)->get();

        $this->view('admin/eleitores/create', [
            'segmentos' => $segmentos,
            'escolas' => $escolas,
            'errors' => $errors,
            'eleitor' => $eleitor
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/eleitores/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:eleitores',
                'documento' => 'required',
                'segmento_id' => 'required',
                'escola_id' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/eleitores/adicionar');
            } else {    
                $eleitor = $this->eleitorModel->create([
                    'nome' => $sanitizedData['nome'],
                    'segmento_id' => $sanitizedData['segmento_id'],
                    'documento' => $sanitizedData['documento'],
                    'escola_id' => $sanitizedData['escola_id'],
                    'status' => $sanitizedData['status']
                ]);
                if ($eleitor) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar eleitor. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/eleitores/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $eleitor = $this->eleitorModel->find($id);
            if (!$eleitor) {
                throw new \Exception('Eleitor não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($eleitor, $key)) {
                    $eleitor->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $segmentos = $this->segmentoModel->where('status', true)->get();
            $escolas = $this->escolaModel->where('status', true)->get();

            $this->view('admin/eleitores/edit', [
                'eleitor' => $eleitor,
                'segmentos' => $segmentos,
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
                $this->redirectToWithMessage('/admin/eleitores/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:eleitores,nome,'.$id,
                'documento' => 'required',
                'segmento_id' => 'required',
                'escola_id' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/eleitores/editar/' . $id);
            } else {
                $eleitor = $this->eleitorModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'segmento_id' => $sanitizedData['segmento_id'],
                    'documento' => $sanitizedData['documento'],
                    'escola_id' => $sanitizedData['escola_id'],
                    'status' => $sanitizedData['status']
                ]);

                if ($eleitor) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar eleitor. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/eleitores/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $eleitor = $this->eleitorModel->find($id);
            if ($eleitor) {
                $this->view('admin/eleitores/show', ['eleitor' => $eleitor]);
            } else {
                $this->renderErrorPage('Erro 404', 'Eleitor não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $eleitor = $this->eleitorModel->delete($id);
        if ($eleitor) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/eleitores/index');
    }

    public function status($id)
    {
        try {
            $reg = $this->eleitorModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $eleitor = $this->eleitorModel->update($id, [
                'status' => $newStatus
            ]);

            if ($eleitor) {
                $this->redirectToWithMessage('/admin/eleitores/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/eleitores/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
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
            'segmento_id' => $this->sanitizer->sanitizeString($_POST['segmento_id']),
            'documento' => $this->sanitizer->sanitizeString($_POST['documento']),
            'escola_id' => $this->sanitizer->sanitizeString($_POST['escola_id']),
            'status' => $this->sanitizer->sanitizeString($_POST['status']),
        ];
    }
}
