<?php
ob_start();
session_start();
date_default_timezone_set('America/Recife');

chdir(dirname(__DIR__)); // Define o diretório de trabalho para a raiz do projeto
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use Dotenv\Dotenv;

// Configurações do Ambiente
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Captura da requisição
$request = Request::capture();

// Obter a URI e o método da requisição
$uri = parse_url($request->server('REQUEST_URI'), PHP_URL_PATH);
$uri = trim($uri, '/');
$method = $request->server('REQUEST_METHOD');

// echo "Captured URI: '$uri'\n";

// Capturar o host da solicitação (caso precise para algo específico)
$host = $_SERVER['HTTP_HOST'];

// Carregar as rotas
require __DIR__ . '/../routes/web.php';

// Despachar a requisição através do roteador
$router->dispatch($uri, $method);

ob_end_flush();
