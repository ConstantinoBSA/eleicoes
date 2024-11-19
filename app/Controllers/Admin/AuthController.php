<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Usuario;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use App\Mail\VerificationEmail;
use App\Models\AccessLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $this->view('admin/auth/login');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = new Usuario();
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $usuarioModel->getByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_authenticated'] = true;

                $accessLog = new AccessLog();
                $accessLog->registerLogin();

                header('Location: /admin/dashboard');
                exit();
            } else {
                $_SESSION['error_message'] = "Usuário ou senha inválidos.";
                header('Location: /admin/login');
                exit();
            }
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /admin/login');
        exit();
    }
}
