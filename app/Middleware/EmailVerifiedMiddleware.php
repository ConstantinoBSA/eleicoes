<?php

namespace App\Middleware;

use App\Models\Usuario;

class EmailVerifiedMiddleware
{
    public function handle($request, $next)
    {
        session_start();
        // Verifique se o usuário está logado e se o e-mail está verificado
        if (isset($_SESSION['user_authenticated'])) {
            $usuarioModel = new Usuario();
            $user = $usuarioModel->getById($_SESSION['user_authenticated']);

            if ($user && $user['email_verified_at'] !== null) {
                // Continue para a próxima etapa do middleware ou para a rota
                return $next($request);
            } else {
                // Redireciona para uma página de erro ou de verificação necessária
                $_SESSION['error_message'] = "Você precisa verificar seu e-mail para acessar esta página.";
                $email = urlencode($user['email']);
                header("Location: /admin/verify-required?email={$email}");
                exit();
            }
        } else {
            // Redireciona para a página de login se o usuário não estiver logado
            header('Location: /admin/login');
            exit();
        }
    }
}
