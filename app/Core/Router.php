<?php

namespace App\Core;

use App\Middleware\AuthMiddleware;

class Router
{
    protected $routes = [];
    protected $protectedRoutes = [];

    public function addRoute($method, $route, $controllerAction, $protected = false)
    {
        $this->routes[strtoupper($method)][$route] = $controllerAction;
        if ($protected) {
            $this->protectedRoutes[] = $route;
        }
    }

    public function dispatch($requestUri, $requestMethod)
    {
        $requestMethod = strtoupper($requestMethod);
        
        foreach ($this->routes[$requestMethod] as $route => $controllerAction) {
            
            // Converter a rota com parâmetros dinâmicos em uma expressão regular
            $routePattern = $this->convertToPattern($route);
            
            // Verificar se a URL requisitada corresponde ao padrão da rota
            if (preg_match($routePattern, $requestUri, $matches)) {
                list($controllerClass, $method) = $controllerAction;

                if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                    $controller = new $controllerClass();

                    // Remover os índices numéricos do array de correspondências
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    // Verifique se a rota é protegida
                    if (in_array($route, $this->protectedRoutes)) {
                        $middleware = new AuthMiddleware();
                        $middleware->handle($_SERVER, function() use ($controller, $method, $params) {
                            call_user_func_array([$controller, $method], $params);
                        });
                    } else {
                        // Chame o controlador diretamente se a rota não for protegida
                        call_user_func_array([$controller, $method], $params);
                    }
                    exit;
                } else {
                    $this->renderErrorPage('Erro 404', 'Controlador ou método não encontrado.');
                }
            }
        }

        // Se nenhuma rota corresponder, renderizar erro 404
        $this->renderErrorPage('Erro 404', 'Rota não encontrada.');
    }

    protected function convertToPattern($route)
    {
        $routePattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            switch ($matches[1]) {
                case 'id':
                    return '(?P<id>\d+)'; // Captura um ID numérico
                case 'slug':
                    return '(?P<slug>[\w\-]+)'; // Captura um slug alfanumérico com hífens
                default:
                    return '(?P<' . $matches[1] . '>[^/]+)'; // Captura qualquer valor para outros parâmetros
            }
        }, $route);
        $routePattern = str_replace('/', '\/', $routePattern);
        return '/^' . $routePattern . '$/';
    }

    protected function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include __DIR__ . '/../../resources/views/errors/default.php';
        exit();
    }
}
