<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Segmento;

class SegmentoController extends Controller
{
    private $segmentoModel;
    private $categoriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->segmentoModel = new Segmento();
        $this->categoriaModel = new Segmento();

        if (!$this->hasPermission('segmentos')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 20;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $segmentos = $this->segmentoModel
                ->where('nome', 'LIKE', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/segmentos/index', [
                'segmentos' => $segmentos,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter segmentos.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $categorias = $this->categoriaModel->where('status', true)->get();

        $this->view('admin/segmentos/create', [
            'categorias' => $categorias,
            'errors' => $errors
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/segmentos/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:segmentos',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                storeOldInput($sanitizedData);
                $this->redirect('/admin/segmentos/adicionar');
            } else {    
                $segmento = $this->segmentoModel->create([
                    'nome' => $sanitizedData['nome'],
                    'status' => $sanitizedData['status']
                ]);
                if ($segmento) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar segmento. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);
                clearOldInput();

                header('Location: /admin/segmentos/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $segmento = $this->segmentoModel->find($id);
            if (!$segmento) {
                throw new \Exception('Segmento não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            unset($_SESSION['errors']);

            $this->view('admin/segmentos/edit', [
                'segmento' => $segmento,
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
                $this->redirectToWithMessage('/admin/segmentos/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:segmentos,nome,'.$id,
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                storeOldInput($sanitizedData);
                $this->redirect('/admin/segmentos/editar/' . $id);
            } else {
                $segmento = $this->segmentoModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'status' => $sanitizedData['status']
                ]);

                if ($segmento) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar segmento. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/segmentos/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $segmento = $this->segmentoModel->find($id);
            if ($segmento) {
                $this->view('admin/segmentos/show', ['segmento' => $segmento]);
            } else {
                $this->renderErrorPage('Erro 404', 'Segmento não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $segmento = $this->segmentoModel->delete($id);
        if ($segmento) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/segmentos/index');
    }

    public function status($id)
    {
        try {
            $reg = $this->segmentoModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $segmento = $this->segmentoModel->update($id, [
                'status' => $newStatus
            ]);

            if ($segmento) {
                $this->redirectToWithMessage('/admin/segmentos/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/segmentos/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
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
            'status' => $this->sanitizer->sanitizeString($_POST['status']),
        ];
    }
}
