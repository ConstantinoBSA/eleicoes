<?php

namespace App\Controllers\Admin;

use Intervention\Image\ImageManagerStatic as Image;
use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\Model;
use App\Core\AuditLogger;
use App\Core\Database;
use App\Core\DB;
use App\Models\Escola;
use App\Models\EscolaSegmento;
use App\Models\Segmento;

class EscolaController extends Controller
{
    private $escolaModel;
    private $categoriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->escolaModel = new Escola();
        $this->categoriaModel = new Escola();

        if (!$this->hasPermission('escolas')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 20;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $escolas = $this->escolaModel
                ->where('nome', 'LIKE', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/escolas/index', [
                'escolas' => $escolas,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter escolas.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $escola = new \stdClass();

        if (!empty($oldData)) {
            foreach ($oldData as $key => $value) {
                $escola->$key = $value;
            }
        }

        $segmentoModel = new Segmento();
        $segmentos = $segmentoModel->get();

        $this->view('admin/escolas/create', [
            'segmentos' => $segmentos,
            'errors' => $errors,
            'escola' => $escola
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/escolas/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                // 'nome' => 'required|unique:escolas',
                // 'sigla' => 'required|unique:escolas',
                'nome' => 'required',
                'sigla' => 'required',
                'endereco' => 'required',
                'segmentos' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/escolas/adicionar');
            } else {    
                $escola = $this->escolaModel->create([
                    'nome' => $sanitizedData['nome'],
                    'sigla' => $sanitizedData['sigla'],
                    'endereco' => $sanitizedData['endereco'],
                    'status' => $sanitizedData['status']
                ]);

                if ($escola) {
                    
                    foreach($sanitizedData['segmentos'] as $value){                        
                        $db = new EscolaSegmento();
                        $db->create([
                            'escola_id' => $escola->id,
                            'segmento_id' => $value
                        ]);
                    }

                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar escola. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/escolas/exibir/'.$escola->id);
            }
        }
    }

    public function edit($id)
    {
        try {
            $escola = $this->escolaModel->find($id);
            if (!$escola) {
                throw new \Exception('Escola não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($escola, $key)) {
                    $escola->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $this->view('admin/escolas/edit', [
                'escola' => $escola,
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
                $this->redirectToWithMessage('/admin/escolas/editar/' . $id, 'Token CSRF inválido.', 'error');
            }
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:escolas,nome,'.$id,
                'endereco' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/escolas/editar/' . $id);
            } else {
                $escola = $this->escolaModel->update($id, [
                    'nome' => $sanitizedData['nome'],
                    'endereco' => $sanitizedData['endereco'],
                    'status' => $sanitizedData['status']
                ]);

                if ($escola) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar escola. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/escolas/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $escola = $this->escolaModel->find($id);
            $segmentoModel = new Segmento();
            $segmentos = $segmentoModel->get();

            $segmentosVinculados = [];
            if ($escola) {
                $segmentosVinculados = array_map(function($segmento) {
                    return $segmento->id;
                }, $escola->segmentos());
            }

            if ($escola) {
                $this->view('admin/escolas/show', [
                    'escola' => $escola,
                    'segmentos' => $segmentos,
                    'segmentosVinculados' => $segmentosVinculados
                ]);
            } else {
                $this->renderErrorPage('Erro 404', 'Escola não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $escola = $this->escolaModel->delete($id);
        if ($escola) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/escolas/index');
    }

    public function vincular($id)
    {
        $pdo = Database::getInstance()->getConnection();

        function vincularSegmentos($pdo, $id, $segmentos)
        {
            // Limpar associações anteriores
            $stmt = $pdo->prepare("DELETE FROM escola_segmentos WHERE escola_id = ?");
            $stmt->execute([$id]);

            // Inserir novas associações
            $stmt = $pdo->prepare("INSERT INTO escola_segmentos (escola_id, segmento_id) VALUES (?, ?)");
            
            foreach ($segmentos as $segmento_id) {
                $stmt->execute([$id, $segmento_id]);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $segmentos = $_POST['segmentos'] ?? []; // Obtém os segmentos selecionados

            vincularSegmentos($pdo, $id, $segmentos);

            // Redirecionar ou exibir uma mensagem de sucesso
            header('Location: /admin/escolas/exibir/'.$id);
            exit;
        }        
    }

    public function status($id)
    {
        try {
            $reg = $this->escolaModel->find($id);
            $currentStatus = $reg->status;
            if ($currentStatus) {
                $newStatus = 0;
            } else {
                $newStatus = 1;
            }

            $escola = $this->escolaModel->update($id, [
                'status' => $newStatus
            ]);

            if ($escola) {
                $this->redirectToWithMessage('/admin/escolas/index', 'Status alterado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/escolas/index', 'Erro ao alterar status. Por favor, tente novamente!', 'error');
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
            'sigla' => $this->sanitizer->sanitizeString($_POST['sigla']),
            'endereco' => $this->sanitizer->sanitizeString($_POST['endereco']),
            'segmentos' => $this->sanitizer->sanitizeArray($_POST['segmentos']),
            'status' => $this->sanitizer->sanitizeString($_POST['status']),
        ];
    }
}
