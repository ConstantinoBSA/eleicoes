<?php

namespace App\Mail;

class PasswordResetEmail extends Mailer
{
    public function sendPasswordReset($email, $token)
    {
        $resetLink = BASE_URL . "/reset-password?token=" . $token;
        $body = "
        <html>
        <body>
            <p>Você solicitou a redefinição de sua senha.</p>
            <p>Clique no link abaixo para redefinir sua senha:</p>
            <p><a href='{$resetLink}'>Redefinir Senha</a></p>
        </body>
        </html>
        ";

        return $this->send($email, 'Redefinição de Senha', $body);
    }
}
