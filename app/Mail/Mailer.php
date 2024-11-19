<?php

namespace App\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Exception;

class Mailer
{
    protected $mailer;

    public function __construct()
    {
        try {
            // Configure o transporte SMTP
            $transport = (new Swift_SmtpTransport('sandbox.smtp.mailtrap.io', 2525))
                ->setUsername('99c5f8322af3ec')
                ->setPassword('ea30379349c54e');

            // Crie o Mailer usando o transporte configurado
            $this->mailer = new Swift_Mailer($transport);
        } catch (Exception $e) {
            throw new Exception('Failed to initialize mailer: ' . $e->getMessage());
        }
    }

    protected function sendEmail($to, $subject, $body)
    {
        // Verifique se o mailer está corretamente inicializado
        if (!$this->mailer) {
            throw new \Exception('Mailer is not initialized.');
        }

        try {
            // Crie a mensagem
            $message = (new Swift_Message($subject))
                ->setFrom(['no-reply@seusite.com' => 'Seu Site'])
                ->setTo([$to => 'Usuário'])
                ->setBody($body, 'text/html');

            // Envie a mensagem
            $result = $this->mailer->send($message);
            return $result;
        } catch (Exception $e) {
            echo 'Erro ao enviar e-mail: ' . $e->getMessage();
        }
    }
}
