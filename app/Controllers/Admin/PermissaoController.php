<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Permissao;

class PermissaoController extends Controller
{
    private $permissaoModel;

    public function __construct()
    {
        parent::__construct();
        $this->permissaoModel = new Permissao();
        
        if (!$this->hasPermission('permissoes')) {
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

            $permissoes = $this->permissaoModel->getAll($search, $limit, $offset);
            $totalPermissoes = $this->permissaoModel->countPermissoes($search);
            $totalPages = ceil($totalPermissoes / $limit);

            $start = $offset + 1;
            $end = min($offset + $limit, $totalPermissoes);

            $this->view('admin/permissoes/index', [
                'permissoes' => $permissoes,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'totalPermissoes' => $totalPermissoes,
                'start' => $start,
                'end' => $end,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter permissoes.');
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

        $this->view('admin/permissoes/create', [
            'errors' => $errors,
            'data' => $oldData
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/permissoes/adicionar', 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => 'required|unique:permissoes',
                'label' => 'required|unique:permissoes',
                'descricao' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/permissoes/adicionar');
            } else {
                try {
                    $permissao = $this->permissaoModel->create($sanitizedData['nome'], $sanitizedData['label'], $sanitizedData['descricao'], $sanitizedData['agrupamento']);
                    if ($permissao) {
                        $this->redirectToWithMessage('/admin/permissoes/index', 'Registro adicionado com sucesso!', 'success');
                    } else {
                        $this->redirectToWithMessage('/admin/permissoes/adicionar', 'Erro ao adicionar permissão. Por favor, tente novamente!', 'error');
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
            $permissao = $this->permissaoModel->getById($id);
            if (!$permissao) {
                throw new \Exception('Permissão não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($permissao, $key)) {
                    $permissao->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $this->view('admin/permissoes/edit', [
                'permissao' => $permissao,
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
                $this->redirectToWithMessage('/admin/permissoes/editar/' . $id, 'Token CSRF inválido.', 'error');
            }

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $rules = [
                'nome' => "required|unique:permissoes,nome,{$id}",
                'label' => "required|unique:permissoes,label,{$id}",
                'descricao' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/permissoes/editar/' . $id);
            } else {
                try {
                    $permissao = $this->permissaoModel->update($id, $sanitizedData['nome'], $sanitizedData['label'], $sanitizedData['descricao'], $sanitizedData['agrupamento']);
                    if ($permissao) {
                        $this->redirectToWithMessage('/admin/permissoes/index', 'Registro editado com sucesso!', 'success');
                    } else {
                        $this->redirectToWithMessage('/admin/permissoes/editar/' . $id, 'Erro ao editar permissao. Por favor, tente novamente!', 'error');
                    }
                } catch (\Exception $e) {
                    $this->handleException($e, 'Erro ao editar a permissao.');
                }
            }
        }
    }

    public function show($id)
    {
        try {
            $permissao = $this->permissaoModel->getById($id);
            if ($permissao) {
                $this->view('admin/permissoes/show', ['permissao' => $permissao]);
            } else {
                $this->renderErrorPage('Erro 404', 'Permissao não encontrada.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $permissao = $this->permissaoModel->delete($id);
            if ($permissao) {
                $this->redirectToWithMessage('/admin/permissoes/index', 'Registro deletado com sucesso!', 'success');
            } else {
                $this->redirectToWithMessage('/admin/permissoes/index', 'Erro ao deletar permissao. Por favor, tente novamente!', 'error');
            }
        } catch (\Exception $e) {
            $this->handleException($e, 'Erro ao deletar a permissao.');
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
            'agrupamento' => $this->sanitizer->sanitizeString($data['agrupamento']),
        ];
    }
}
