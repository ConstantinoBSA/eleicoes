<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;

use App\Mail\VerificationEmail;
use App\Mail\PasswordResetEmail;

class UsuarioController extends Controller
{
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 4; // Número de usuarios por página
        $offset = ($page - 1) * $limit;
        
        $usuarioModel = $this->model('Usuario');
        $usuarios = $usuarioModel->getAll($search, $limit, $offset);
        $totalUsuarios = $usuarioModel->countUsuarios($search);
        $totalPages = ceil($totalUsuarios / $limit);

        $start = $offset + 1;
        $end = min($offset + $limit, $totalUsuarios);

        $this->view('usuarios/index', [
            'usuarios' => $usuarios,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalUsuarios' => $totalUsuarios,
            'start' => $start,
            'end' => $end,
            'search' => $search
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $validator = new Validator();
            $rules = [
                'name' => 'required',
                'email' => 'required',
                'status' => 'required'
            ];

            $errors = $validator->validate($_POST, $rules);

            if (!empty($errors)) {
                $this->view('usuarios/create', ['error' => $errors, 'data' => $_POST]);
            } else {
                $usuarioModel = $this->model('Usuario');
                $usuario = $usuarioModel->create($_POST['name'], $_POST['email'], $_POST['status']);

                // Gere um token único e seguro
                $token = bin2hex(random_bytes(50));

                // Armazene o token de verificação no banco de dados
                $usuarioModel->storeEmailVerificationToken($usuario['email'], $token);
                new VerificationEmail($usuario, $token);

                if ($usuario) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar usuario. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);
                
                header('Location: /usuarios/index');
            }
        } else {
            $this->view('usuarios/create', ['error' => [], 'data' => []]);
        }
    }

    public function edit($id)
    {
        $usuarioModel = $this->model('Usuario');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Token CSRF inválido.');
            }

            $usuario = $usuarioModel->update($id, $_POST['email']);

            // Token válido, remova-o da sessão
            unset($_SESSION['csrf_token']);

            header('Location: /usuarios/index');
        } else {
            $usuario = $usuarioModel->getById($id);
            if ($usuario) {
                $this->view('usuarios/edit', ['data' => $usuario]);
            } else {
                $this->renderErrorPage('Erro 404', 'Usuário não encontrado.');
            }
        }
    }

    public function delete($id)
    {
        $usuarioModel = $this->model('Usuario');
        $usuarioModel->delete($id);
        header('Location: /usuarios/index');
    }

    // Função para renderizar a view de erro
    private function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include 'views/error.php';
        exit();
    }
}
