<?php

namespace App\Mail;

class VerificationEmail extends Mailer
{
    public $usuario;
    public $token;

    public function __construct($usuario, $token)
    {
        parent::__construct(); // Certifique-se de chamar o construtor da classe pai
        $this->usuario = $usuario;
        $this->token = $token;

        $this->sendVerification();
    }

    protected function bodyEmail()
    {
        $resetLink = BASE_URL . "/verify-email?token=" . $this->token;

        

        $emailTemplate = file_get_contents('../resources/views/emails/verificacao_email.php');
        $emailContent = str_replace([
                '{nome_usuario}',
                '{verificationLink}',
            ], [
                $this->usuario['name'],
                $resetLink,
            ], $emailTemplate);

        return $emailContent;
    }

    public function sendVerification()
    {
        return $this->sendEmail($this->usuario['email'], 'Verificação de E-mail', $this->bodyEmail());
    }
}

