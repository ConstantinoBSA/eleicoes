<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        // Exemplo de lógica para verificar autenticação
        if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
            // Redirecionar ou exibir erro se não autenticado
            header("Location: /admin/login");
            exit();
        }

        // Se autenticado, continua para o próximo passo
        return $next();
    }
}
