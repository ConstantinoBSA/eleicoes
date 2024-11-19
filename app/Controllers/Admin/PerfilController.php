<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Perfil;

class PerfilController extends Controller
{
    private $perfilModel;

    public function __construct()
    {
        parent::__construct();
        $this->perfilModel = new Perfil();
        
        if (!$this->hasPermission('perfis')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $search = $this->request->all()['search'] ?? '';
            $page = $this->request->all()['page'] ?? 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $perfis = $this->perfilModel->getAll($search, $limit, $offset);
            $totalPerfis = $this->perfilModel->countPerfis($search);
            $totalPages = ceil($totalPerfis / $limit);

            $start = $offset + 1;
            $end = min($offset + $limit, $totalPerfis);

            $permissoes = $this->perfilModel->getPermissoes();

            $permissoesAgrupadas = [];
            foreach ($permissoes as $row) {
                $agrupamento = $row['agrupamento'] ?: 'Sem Grupo';  // Use 'Outros' para permissões sem agrupamento
                if (!isset($permissoesAgrupadas[$agrupamento])) {
                    $permissoesAgrupadas[$agrupamento] = [];
                }
                $permissoesAgrupadas[$agrupamento][] = $row;
            }

            $this->view('admin/perfis/index', [
                'perfis' => $perfis,
                'permissoes' => $permissoes,
                'permissoesAgrupadas' => $permissoesAgrupadas,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'totalPerfis' => $totalPerfis,
                'start' => $start,
                'end' => $end,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter perfis.');
        }
    }

    public function create()
    {
        // if (!$this->hasPermission('gerenciar_usuario')) {
        //     abort('403', 'Você não tem acesso a está área do sistema');
        // }

        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $this->view('admin/perfis/create', [
            'errors' => $errors,
            'data' => $oldData
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/perfis/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:perfis',
                'label' => 'required|unique:perfis',
                'descricao' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/perfis/adicionar');
            } else {
                try {
                    $perfil = $this->perfilModel->create($sanitizedData['nome'], $sanitizedData['label'], $sanitizedData['descricao']);
                    if ($perfil) {
                        $this->redirectToWithMessage('/admin/perfis/index', 'Registro adicionado com sucesso!', 'success');
                    } else {
                        $this->redirectToWithMessage('/admin/perfis/adicionar', 'Erro ao adicionar permissão. Por favor, tente novamente!', 'error');
                    }
                } catch (\Exception $e) {
                    $this->handleException($e, 'Erro ao adicionar a permissão.');
                }
            }
        }
    }

    public function edit($id)
    {
        try {
            $perfil = $this->perfilModel->getById($id);
            if (!$perfil) {
                throw new \Exception('Permissão não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($perfil, $key)) {
                    $perfil->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $this->view('admin/perfis/edit', [
                'perfil' => $perfil,
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
                $this->redirectToWithMessage('/admin/perfis/editar/' . $id, 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => "required|unique:perfis,nome,{$id}",
                'label' => "required|unique:perfis,label,{$id}",
                'descricao' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/perfis/editar/' . $id);
            } else {
                try {
                    $perfil = $this->perfilModel->update($id, $sanitizedData['nome'], $sanitizedData['label'], $sanitizedData['descricao']);
                    if ($perfil) {
                        $this->redirectToWithMessage('/admin/perfis/index', 'Registro editado com sucesso!', 'success');
                    } else {
                        $this->redirectToWithMessage('/admin/perfis/editar/' . $id, 'Erro ao editar perfil. Por favor, tente novamente!', 'error');
                    }
                } catch (\Exception $e) {
                    $this->handleException($e, 'Erro ao editar a perfil.');
                }
            }
        }
    }

    public function show($id)
    {
        try {
            $perfil = $this->perfilModel->getById($id);
            if ($perfil) {
                $this->view('admin/perfis/show', ['perfil' => $perfil]);
            } else {
                $this->renderErrorPage('Erro 404', 'Perfil não encontrada.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $perfil = $this->perfilModel->delete($id);
            if ($perfil) {
                $this->redirectToWithMessage('/admin/perfis/index', 'Registro deletado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/perfis/index', 'Erro ao deletar perfil. Por favor, tente novamente!', 'error');
            }
        } catch (\Exception $e) {
            $this->handleException($e, 'Erro ao deletar a perfil.');
        }
    }

    private function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include __DIR__ . '/../../../resources/views/errors/default.php';
        exit();
    }

    private function sanitizeData($data)
    {
        return [
            'nome' => $this->sanitizer->sanitizeString($data['nome']),
            'label' => $this->sanitizer->sanitizeString($data['label']),
            'descricao' => $this->sanitizer->sanitizeString($data['descricao']),
        ];
    }

    public function permissoes()
    {
        $pdo = Database::getInstance()->getConnection();
        // Para corresponder aos dados enviados via AJAX
        $permissionName = $_POST['permissoes'] ?? null;
        $perfilId = $_POST['perfil_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$permissionName || !$perfilId || !$status) {
            echo json_encode(['message' => 'Dados inválidos.']);
            return;
        }

        try {
            if ($status === 'grant') {
                // Conceder permissão
                $permissionId = $this->getPermissionIdByName($permissionName, $pdo);
                $stmt = $pdo->prepare("INSERT IGNORE INTO permissao_perfil (perfil_id, permissao_id) VALUES (?, ?)");
                $stmt->execute([$perfilId, $permissionId]);

                echo json_encode(['message' => 'Permissão concedida.']);
            } else {
                // Revogar permissão
                $permissionId = $this->getPermissionIdByName($permissionName, $pdo);
                $stmt = $pdo->prepare("DELETE FROM permissao_perfil WHERE perfil_id = ? AND permissao_id = ?");
                $stmt->execute([$perfilId, $permissionId]);

                echo json_encode(['message' => 'Permissão revogada.']);
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            echo json_encode(['message' => 'Erro ao alterar permissão.']);
        }
    }

    private function getPermissionIdByName($permissionName, $pdo)
    {
        $stmt = $pdo->prepare("SELECT id FROM permissoes WHERE nome = ?");
        $stmt->execute([$permissionName]);
        return $stmt->fetchColumn();
    }
}
