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

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = new Usuario();

            $email = $_POST['email'];
            // Verifique se o e-mail existe no banco de dados
            $user = $usuarioModel->findUserByEmail($email);
        
            if ($user) {
                // Gere um token único e seguro
                $token = bin2hex(random_bytes(50));
        
                // Armazene o token e o timestamp no banco de dados
                $usuarioModel->storeResetToken($email, $token);
        
                // Envie o e-mail com o link de redefinição de senha
                $resetLink = BASE_URL . "/reset-password?token=" . $token;

                // Configuração do transporte SMTP
                $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
                    ->setUsername('99c5f8322af3ec') // Substitua pelo seu usuário do Mailtrap
                    ->setPassword('ea30379349c54e'); // Substitua pela sua senha do Mailtrap

                // Criação do Mailer usando o transporte configurado
                $mailer = new Swift_Mailer($transport);

                $emailTemplate = file_get_contents('../resources/views/emails/redefinicao_senha.php');
                $emailContent = str_replace([
                        '{nome_usuario}',
                        '{reset_link}',
                    ], [
                        $user['name'],
                        $resetLink,
                    ], $emailTemplate);

                // Criação da mensagem
                $message = (new Swift_Message('Redefinição de Senha'))
                    ->setFrom(['no-reply@seusite.com' => 'Seu Site'])
                    ->setTo([$email => 'Usuário'])
                    ->setBody($emailContent, 'text/html');

                // Envio da mensagem
                $mailer->send($message);
        
                $_SESSION['flash_message'] = "Um link de redefinição de senha foi enviado para o seu e-mail.";
            } else {
                $_SESSION['flash_message'] = "E-mail não encontrado.";
            }

            header("Location: /admin/forgot-password");
            // exit();
        } else {
            $this->view('admin/auth/forgot-password');
        }
    }

    public function resetPassword()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        
            $usuarioModel = new Usuario();
        
            // Verifique se o token é válido e busque o e-mail associado
            $email = $usuarioModel->findEmailByToken($token);
        
            if ($email) {
                // Atualize a senha no banco de dados
                $usuarioModel->updatePassword($email, $newPassword);
        
                // Invalide o token
                $usuarioModel->invalidateToken($token);
        
                // Armazene uma mensagem de sucesso na sessão
                $_SESSION['message'] = "Sua senha foi redefinida com sucesso!";
                $_SESSION['message_type'] = "success";
            } else {
                // Armazene uma mensagem de erro na sessão
                $_SESSION['message'] = "Token inválido ou expirado!";
                $_SESSION['message_type'] = "error";
            }
        
            // Redirecione para a página de login ou outra página apropriada
            header("Location: /admin/login");
            exit();
        } else {
            // Capture o token da URL
            $token = $_GET['token'] ?? null;

            $this->view('admin/auth/reset-password', ['token' => $token]);
        }
    }

    public function verifyRequired()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['email'])) {
                $email = $_POST['email'];

                $usuarioModel = new Usuario();
                $usuario = $usuarioModel->getById($_SESSION['user_id']);

                // Gere um token único e seguro
                $token = bin2hex(random_bytes(50));

                // Armazene o token de verificação no banco de dados
                $usuarioModel->storeEmailVerificationToken($email, $token);
                new VerificationEmail($usuario, $token);

                if ($usuario) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar usuario. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }
                header('Location: /admin/verify-required');
            } else {
                // Se o token não estiver presente, redirecione ou mostre um erro
                $_SESSION['error_message'] = "Token de verificação ausente.";
                $this->view('admin/auth/verify-required');
            }
        } else {
            
            $this->view('admin/auth/verify-required');
        }
    }

    public function verifyEmail()
    {
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $usuarioModel = new Usuario();

            // Verifique se o token é válido e obtenha o e-mail associado
            $email = $usuarioModel->findEmailVerificationByToken($token);

            if ($email) {
                // Atualize o status do e-mail no banco de dados para verificado
                $usuarioModel->verifyUserEmail($email);

                // Remova o token de verificação
                $usuarioModel->invalidateVerificationToken($token);

                // Armazene uma mensagem de sucesso na sessão
                $_SESSION['flash_message'] = "E-mail verificado com sucesso!";
                header('Location: /admin/login');
                exit();
            } else {
                // Armazene uma mensagem de erro na sessão
                $_SESSION['error_message'] = "Token inválido ou expirado.";
                $this->view('admin/auth/verification-email');
            }
        } else {
            // Se o token não estiver presente, redirecione ou mostre um erro
            $_SESSION['error_message'] = "Token de verificação ausente.";
            $this->view('auth/verification-email');
        }
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

                $accessLog = new AccessLog();
                $accessLog->registerLogin();

                header('Location: /admin');
                exit();
            } else {
                session_start();
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
