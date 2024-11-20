<?php

namespace App\Core;

class Controller
{
    protected $sanitizer;
    protected $validator;
    protected $auditLogger;
    protected $request;
    protected $redirect;
    protected $instances = [];

    public function __construct()
    {
        $this->sanitizer = new Sanitizer();
        $this->validator = new Validator();
        $this->auditLogger = new AuditLogger();
        $this->request = new Request();
        $this->redirect = new Redirect();
    }

    public function view($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../resources/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "View not found: $viewPath"; // Debugging
        }
    }

    protected function redirect($location)
    {
        // Extrai a URI atual completa
        $currentUri = $this->getPreviousRoute();

        // Determina a posição do '?' na URL
        $queryStringStart = strpos($currentUri, '?');

        // Se existir uma query string, extraí-la
        if ($queryStringStart !== false) {
            // Obtém a query string completa a partir do '?'
            $queryString = substr($currentUri, $queryStringStart);
        } else {
            // Se não houver uma query string, define-a como vazia
            $queryString = '';
        }

        // Monta a URL completa combinando o destino com a query string atual
        $url = $location . $queryString;

        // Executa o redirecionamento
        header('Location: ' . $url);
        exit();
    }

    protected function getPreviousRoute()
    {
        // Verifica se o cabeçalho HTTP_REFERER está definido
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            // Retorna null ou uma string padrão se o referer não estiver definido
            return null; // ou uma URL padrão
        }
    }

    protected function redirectToWithMessage($location, $message, $type)
    {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
        $this->redirect($location);
    }

    protected function validateCsrfToken($token)
    {
        return isset($token) && $token === $_SESSION['csrf_token'];
    }

    protected function handleException($exception, $userMessage, $redirectLocation = '/')
    {
        $this->renderErrorPage($exception->getMessage(), $exception);
    }

    protected function hasProfile($profileName)
    {
        return hasProfile($profileName);
    }

    protected function hasPermission($permissionName)
    {
        return hasPermission($permissionName);
    }

    // Função para renderizar a view de erro
    private function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include __DIR__.'/../../resources/views/errors/default.php';
        exit();
    }
}
