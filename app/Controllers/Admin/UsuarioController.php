<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Perfil;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    private $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Usuario();
        
        if (!$this->hasPermission('usuarios')) {
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

            $usuarios = $this->usuarioModel->getAll($search, $limit, $offset);
            $totalUsuarios = $this->usuarioModel->countUsuarios($search);
            $totalPages = ceil($totalUsuarios / $limit);

            $start = $offset + 1;
            $end = min($offset + $limit, $totalUsuarios);

            $perfis = $this->usuarioModel->getPerfis();

            $this->view('admin/usuarios/index', [
                'usuarios' => $usuarios,
                'perfis' => $perfis,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'totalUsuarios' => $totalUsuarios,
                'start' => $start,
                'end' => $end,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter usuarios.');
        }
    }

    public function create()
    {
        // if (!$this->hasPerfil('gerenciar_usuario')) {
        //     abort('403', 'Você não tem acesso a está área do sistema');
        // }

        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $this->view('admin/usuarios/create', [
            'errors' => $errors,
            'data' => $oldData
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/usuarios/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:usuarios',
                'label' => 'required|unique:usuarios',
                'descricao' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/usuarios/adicionar');
            } else {
                try {
                    $usuario = $this->usuarioModel->create($sanitizedData['nome'], $sanitizedData['label'], $sanitizedData['descricao']);
                    if ($usuario) {
                        $this->redirectToWithMessage('/admin/usuarios/index', 'Registro adicionado com sucesso!', 'success');
                    } else {
                        $this->redirectToWithMessage('/admin/usuarios/adicionar', 'Erro ao adicionar permissão. Por favor, tente novamente!', 'error');
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
            $usuario = $this->usuarioModel->getById($id);
            if (!$usuario) {
                throw new \Exception('Permissão não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($usuario, $key)) {
                    $usuario->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $this->view('admin/usuarios/edit', [
                'usuario' => $usuario,
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
                $this->redirectToWithMessage('/admin/usuarios/editar/' . $id, 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => "required|unique:usuarios,nome,{$id}",
                'label' => "required|unique:usuarios,label,{$id}",
                'descricao' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/usuarios/editar/' . $id);
            } else {
                try {
                    $usuario = $this->usuarioModel->update($id, $sanitizedData['nome'], $sanitizedData['label'], $sanitizedData['descricao']);
                    if ($usuario) {
                        $this->redirectToWithMessage('/admin/usuarios/index', 'Registro editado com sucesso!', 'success');
                    } else {
                        $this->redirectToWithMessage('/admin/usuarios/editar/' . $id, 'Erro ao editar usuario. Por favor, tente novamente!', 'error');
                    }
                } catch (\Exception $e) {
                    $this->handleException($e, 'Erro ao editar a usuario.');
                }
            }
        }
    }

    public function show($id)
    {
        try {
            $usuario = $this->usuarioModel->getById($id);
            if ($usuario) {
                $this->view('admin/usuarios/show', ['usuario' => $usuario]);
            } else {
                $this->renderErrorPage('Erro 404', 'Usuario não encontrada.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $usuario = $this->usuarioModel->delete($id);
            if ($usuario) {
                $this->redirectToWithMessage('/admin/usuarios/index', 'Registro deletado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/usuarios/index', 'Erro ao deletar usuario. Por favor, tente novamente!', 'error');
            }
        } catch (\Exception $e) {
            $this->handleException($e, 'Erro ao deletar a usuario.');
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

    public function perfis()
    {
        $pdo = Database::getInstance()->getConnection();
        // Para corresponder aos dados enviados via AJAX
        $perfilName = $_POST['perfis'] ?? null;
        $usuarioId = $_POST['usuario_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$perfilName || !$usuarioId || !$status) {
            echo json_encode(['message' => 'Dados inválidos.']);
            return;
        }

        try {
            if ($status === 'grant') {
                // Conceder permissão
                $perfilId = $this->getPerfilIdByName($perfilName, $pdo);
                $stmt = $pdo->prepare("INSERT IGNORE INTO perfil_usuario (usuario_id, perfil_id) VALUES (?, ?)");
                $stmt->execute([$usuarioId, $perfilId]);

                echo json_encode(['message' => 'Permissão concedida.']);
            } else {
                // Revogar permissão
                $perfilId = $this->getPerfilIdByName($perfilName, $pdo);
                $stmt = $pdo->prepare("DELETE FROM perfil_usuario WHERE usuario_id = ? AND perfil_id = ?");
                $stmt->execute([$usuarioId, $perfilId]);

                echo json_encode(['message' => 'Permissão revogada.']);
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            echo json_encode(['message' => 'Erro ao alterar permissão.']);
        }
    }

    private function getPerfilIdByName($perfilName, $pdo)
    {
        $stmt = $pdo->prepare("SELECT id FROM perfis WHERE nome = ?");
        $stmt->execute([$perfilName]);
        return $stmt->fetchColumn();
    }
}
