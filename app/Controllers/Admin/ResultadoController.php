<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Resultado;

class ResultadoController extends Controller
{
    private $resultadoModel;
    private $categoriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->resultadoModel = new Resultado();
        $this->categoriaModel = new Resultado();

        if (!$this->hasPermission('resultados')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 20;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $resultados = $this->resultadoModel
                ->where('nome', 'LIKE', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/resultados/index', [
                'resultados' => $resultados,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter resultados.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $resultado = new \stdClass();

        if (!empty($oldData)) {
            foreach ($oldData as $key => $value) {
                $resultado->$key = $value;
            }
        }

        $categorias = $this->categoriaModel->where('status', true)->get();

        $this->view('admin/resultados/create', [
            'categorias' => $categorias,
            'errors' => $errors,
            'resultado' => $resultado
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/resultados/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:resultados',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/resultados/adicionar');
            } else {    
                $resultado = $this->resultadoModel->create([
                    'nome' => $sanitizedData['nome'],
                    'status' => $sanitizedData['status']
                ]);
                if ($resultado) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar resultado. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/resultados/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $resultado = $this->resultadoModel->find($id);
            if (!$resultado) {
                throw new \Exception('Resultado não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($resultado, $key)) {
                    $resultado->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $this->view('admin/resultados/edit', [
                'resultado' => $resultado,
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
                $this->redirectToWithMessage('/admin/resultados/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:resultados,nome,'.$id,
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/resultados/editar/' . $id);
            } else {
                $resultado = $this->resultadoModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'status' => $sanitizedData['status']
                ]);

                if ($resultado) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar resultado. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/resultados/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $resultado = $this->resultadoModel->find($id);
            if ($resultado) {
                $this->view('admin/resultados/show', ['resultado' => $resultado]);
            } else {
                $this->renderErrorPage('Erro 404', 'Resultado não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $resultado = $this->resultadoModel->delete($id);
        if ($resultado) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/resultados/index');
    }

    public function status($id)
    {
        try {
            $reg = $this->resultadoModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $resultado = $this->resultadoModel->update($id, [
                'status' => $newStatus
            ]);

            if ($resultado) {
                $this->redirectToWithMessage('/admin/resultados/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/resultados/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
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
