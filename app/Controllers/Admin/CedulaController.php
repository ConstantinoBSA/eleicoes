<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Cedula;
use App\Models\Escola;

class CedulaController extends Controller
{
    private $cedulaModel;
    private $escolaModel;

    public function __construct()
    {
        parent::__construct();
        $this->cedulaModel = new Cedula();
        $this->escolaModel = new Escola();

        if (!$this->hasPermission('cedulas')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $cedulas = $this->cedulaModel->mostrarCedulas();

            $this->view('admin/cedulas/index', [
                'cedulas' => $cedulas,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter cedulas.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $cedula = new \stdClass();

        if (!empty($oldData)) {
            foreach ($oldData as $key => $value) {
                $cedula->$key = $value;
            }
        }

        $escolas = $this->escolaModel->where('status', true)->get();

        $this->view('admin/cedulas/create', [
            'escolas' => $escolas,
            'errors' => $errors,
            'cedula' => $cedula
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/cedulas/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'escola_id' => 'required',
                'tipo' => 'required',
                'quantidade' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/cedulas/adicionar');
            } else {    
                $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
                $escola_id = $sanitizedData['escola_id'];
                $tipo = $sanitizedData['tipo'];

                if ($quantidade > 0) {                  
                    for ($i = '0'; $i < $quantidade; $i++) {
                        $codigoSeguranca = gerarCodigoSeguranca($escola_id, $tipo);
                        $cedula = $this->cedulaModel->create([
                            'codigo_seguranca' => $codigoSeguranca,
                            'escola_id' => $escola_id,
                            'tipo' => $tipo
                        ]);
                    }
                }
                
                if ($cedula) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar cedula. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/cedulas/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $cedula = $this->cedulaModel->find($id);
            if (!$cedula) {
                throw new \Exception('Cedula não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($cedula, $key)) {
                    $cedula->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $this->view('admin/cedulas/edit', [
                'cedula' => $cedula,
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
                $this->redirectToWithMessage('/admin/cedulas/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:cedulas,nome,'.$id,
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/cedulas/editar/' . $id);
            } else {
                $cedula = $this->cedulaModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'status' => $sanitizedData['status']
                ]);

                if ($cedula) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar cedula. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/cedulas/index');
            }
        }
    }

    public function gerenciar($id)
    {
        try {
            $escola = $this->escolaModel->find($id);
            $cedulas = $this->cedulaModel->where('escola_id', $id)->get();
            
            if ($escola) {
                $this->view('admin/cedulas/gerenciar', [
                    'escola' => $escola,
                    'cedulas' => $cedulas
                ]);
            } else {
                $this->renderErrorPage('Erro 404', 'Cedula não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $cedula = $this->cedulaModel->delete($id);
        if ($cedula) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/cedulas/index');
    }

    public function status($id)
    {
        try {
            $reg = $this->cedulaModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $cedula = $this->cedulaModel->update($id, [
                'status' => $newStatus
            ]);

            if ($cedula) {
                $this->redirectToWithMessage('/admin/cedulas/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/cedulas/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
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
            // 'codigo_seguranca' => $this->sanitizer->sanitizeString($_POST['codigo_seguranca']),
            'escola_id' => $this->sanitizer->sanitizeInt($_POST['escola_id']),
            // 'eleitor_id' => $this->sanitizer->sanitizeInt($_POST['eleitor_id']),
            'tipo' => $this->sanitizer->sanitizeString($_POST['tipo']),
            // 'usado' => $this->sanitizer->sanitizeString($_POST['usado']),
            // 'data_emissao' => $this->sanitizer->sanitizeString($_POST['data_emissao']),
            'quantidade' => $this->sanitizer->sanitizeString($_POST['quantidade']),
        ];
    }
}
