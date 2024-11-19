<?php

use App\Core\Auth;
use App\Core\Database;
use App\Models\Configuracao;

function dd(...$terms)
{
    echo '<pre>';
    foreach ($terms as $term) {
        print_r($term);
    }
    echo '</pre>';
    die;
}

function generateSixDigitPassword() {
    // Gera um número aleatório entre 100000 e 999999
    return random_int(100000, 999999);
}

if (!function_exists('auth')) {
    function auth() {
        $auth = new Auth();
        return $auth;
    }
}

function generateSlug($string) {
    // Converte para minúsculas
    $string = strtolower($string);
    // Remove caracteres especiais
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    // Substitui espaços e múltiplos hifens por um único hífen
    $string = preg_replace('/[\s-]+/', '-', $string);
    // Remove hifens no início e no final
    $string = trim($string, '-');
    return $string;
}

function hasProfile($profileName) {
    $pdo = Database::getInstance()->getConnection();
    $query = "SELECT COUNT(*) FROM perfis 
        JOIN perfil_usuario ON perfis.id = perfil_usuario.perfil_id
        WHERE perfil_usuario.usuario_id = ? AND perfis.nome = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([auth()->user()->id, $profileName]);
    $result = $stmt->fetchColumn();

    return $result > 0;
}

function hasPermission($permissionName) {
    if(hasProfile('administrador')){
        return true;
    }else{
        $pdo = Database::getInstance()->getConnection();
        $query = "SELECT COUNT(*) FROM permissoes 
            JOIN permissao_perfil ON permissoes.id = permissao_perfil.permissao_id
            JOIN perfil_usuario ON permissao_perfil.perfil_id = perfil_usuario.perfil_id
            WHERE perfil_usuario.usuario_id = ? AND permissoes.nome = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([auth()->user()->id, $permissionName]);
        $result = $stmt->fetchColumn();
        
        if ($result > 0) {
            return true;
        }

        // Verifique permissões diretas atribuídas ao usuário
            $query = "SELECT COUNT(*) FROM permissoes 
            JOIN permissao_usuario ON permissoes.id = permissao_usuario.permissao_id
            WHERE permissao_usuario.usuario_id = ? AND permissoes.nome = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([auth()->user()->id, $permissionName]);
        $result = $stmt->fetchColumn();

        if ($result > 0) {
            return true;
        }
    }  
}

function abort($code, $mensagem)
{
    $errorTitle = $code;
    $errorMessage = $mensagem;
    include __DIR__ . '/../../resources/views/errors/'.$code.'.php';
    exit();
}

function isActiveSection($sectionName)
{
    // Obtenha a URL do caminho atual
    $currentRoute = strtok($_SERVER['REQUEST_URI'], '?');

    // Verifique se a URL atual contém o nome da seção
    return strpos($currentRoute, '/' . $sectionName) !== false ? 'active' : '';
}

function config()
{
    $configModel = new Configuracao;
    $configuracoes = $configModel->getAllConfiguracoes();

    return $configuracoes;
}

function isRouter($route) {
    // Obter o URI da requisição atual
    $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    // Comparar com o route fornecido
    return $currentUri === trim($route, '/');
}

function csrf()
{
    if (!empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        echo '<input type="hidden" name="csrf_token" value="'. htmlspecialchars($_SESSION['csrf_token'] ?? '').'">';
    }
}

function gerarCodigoSeguranca() {
    return bin2hex(random_bytes(16));  // Gera um código de 32 caracteres hexadecimais
}

function gerarCedulasParaEscola($escolaId, $quantidade) {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("INSERT INTO cedulas (codigo_seguranca, escola_id) VALUES (:codigo, :escola_id)");
    for ($i = 0; $i < $quantidade; $i++) {
        $codigoSeguranca = gerarCodigoSeguranca();
        $stmt->execute(['codigo' => $codigoSeguranca, 'escola_id' => $escolaId]);
    }
}
