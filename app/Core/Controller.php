<?php

namespace App\Core;

class Controller
{
    protected $sanitizer;
    protected $validator;
    protected $auditLogger;
    protected $request;
    protected $instances = [];

    public function __construct()
    {
        $this->sanitizer = new Sanitizer();
        $this->validator = new Validator();
        $this->auditLogger = new AuditLogger();
        $this->request = new Request();
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
        header('Location: ' . $location);
        exit();
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
