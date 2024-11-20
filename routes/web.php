<?php

use App\Core\Router;

use App\Controllers\Site\IndexController;

use App\Controllers\Admin\HomeController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\PermissaoController;
use App\Controllers\Admin\PerfilController;
use App\Controllers\Admin\UsuarioController;

use App\Controllers\Admin\SegmentoController;
use App\Controllers\Admin\EscolaController;
use App\Controllers\Admin\CandidatoController;
use App\Controllers\Admin\EleitorController;
use App\Controllers\Admin\ChapaController;
use App\Controllers\Admin\CedulaController;
use App\Controllers\Admin\ResultadoController;

use App\Controllers\Admin\ImpressaoController;
use App\Controllers\Admin\ConsultaController;
use App\Controllers\Admin\RelatorioController;
use App\Controllers\Admin\AjaxController;

$router = new Router();

// Verifique se a URL atual é a raiz
if (isRouter('')) {
    // Redirecionar para /home
    header('Location: /home');
    exit();
}

// Site
$router->addRoute('GET', 'home', [IndexController::class, 'index']);
$router->addRoute('GET', 'cedulas', [IndexController::class, 'cedulas']);
$router->addRoute('GET', 'resultados', [IndexController::class, 'resultados']);


if (isRouter('admin')) {
    // Redirecionar para /home
    header('Location: /admin/dashboard');
    exit();
}

// Admin
// Auth
$router->addRoute('GET', 'admin/forgot-password', [AuthController::class, 'forgotPassword']);
$router->addRoute('POST', 'admin/reset-password', [AuthController::class, 'resetPassword']);
$router->addRoute('GET', 'admin/verify-required', [AuthController::class, 'verifyRequired']);
$router->addRoute('POST', 'admin/verify-email', [AuthController::class, 'verifyEmail']);
$router->addRoute('GET', 'admin/login', [AuthController::class, 'showLoginForm']);
$router->addRoute('POST', 'admin/login', [AuthController::class, 'login']);
$router->addRoute('GET', 'admin/logout', [AuthController::class, 'logout'], true);

$router->addRoute('GET', 'admin/dashboard', [HomeController::class, 'index'], true);
$router->addRoute('GET', 'admin/perfil-usuario', [HomeController::class, 'perfil'], true);
$router->addRoute('GET', 'admin/configuracoes', [HomeController::class, 'configuracoes'], true);

// Permissões
$router->addRoute('GET', 'admin/permissoes/index', [PermissaoController::class, 'index'], true);
$router->addRoute('GET', 'admin/permissoes/adicionar', [PermissaoController::class, 'create'], true);
$router->addRoute('POST', 'admin/permissoes/store', [PermissaoController::class, 'store'], true);
$router->addRoute('GET', 'admin/permissoes/editar/{id}', [PermissaoController::class, 'edit'], true);
$router->addRoute('POST', 'admin/permissoes/update/{id}', [PermissaoController::class, 'update'], true);
$router->addRoute('GET', 'admin/permissoes/exibir/{id}', [PermissaoController::class, 'show'], true);
$router->addRoute('GET', 'admin/permissoes/delete/{id}', [PermissaoController::class, 'delete'], true);

// Perfis
$router->addRoute('GET', 'admin/perfis/index', [PerfilController::class, 'index'], true);
$router->addRoute('GET', 'admin/perfis/adicionar', [PerfilController::class, 'create'], true);
$router->addRoute('POST', 'admin/perfis/store', [PerfilController::class, 'store'], true);
$router->addRoute('GET', 'admin/perfis/editar/{id}', [PerfilController::class, 'edit'], true);
$router->addRoute('POST', 'admin/perfis/update/{id}', [PerfilController::class, 'update'], true);
$router->addRoute('GET', 'admin/perfis/exibir/{id}', [PerfilController::class, 'show'], true);
$router->addRoute('GET', 'admin/perfis/delete/{id}', [PerfilController::class, 'delete'], true);
$router->addRoute('POST', 'admin/perfis/permissoes', [PerfilController::class, 'permissoes'], true);

// Usuários
$router->addRoute('GET', 'admin/usuarios/index', [UsuarioController::class, 'index'], true);
$router->addRoute('GET', 'admin/usuarios/adicionar', [UsuarioController::class, 'create'], true);
$router->addRoute('POST', 'admin/usuarios/store', [UsuarioController::class, 'store'], true);
$router->addRoute('GET', 'admin/usuarios/editar/{id}', [UsuarioController::class, 'edit'], true);
$router->addRoute('POST', 'admin/usuarios/update/{id}', [UsuarioController::class, 'update'], true);
$router->addRoute('GET', 'admin/usuarios/exibir/{id}', [UsuarioController::class, 'show'], true);
$router->addRoute('GET', 'admin/usuarios/delete/{id}', [UsuarioController::class, 'delete'], true);
$router->addRoute('POST', 'admin/usuarios/perfis', [UsuarioController::class, 'perfis'], true);

// Segmentos
$router->addRoute('GET', 'admin/segmentos/index', [SegmentoController::class, 'index'], true);
$router->addRoute('GET', 'admin/segmentos/adicionar', [SegmentoController::class, 'create'], true);
$router->addRoute('POST', 'admin/segmentos/store', [SegmentoController::class, 'store'], true);
$router->addRoute('GET', 'admin/segmentos/editar/{id}', [SegmentoController::class, 'edit'], true);
$router->addRoute('POST', 'admin/segmentos/update/{id}', [SegmentoController::class, 'update'], true);
$router->addRoute('GET', 'admin/segmentos/exibir/{id}', [SegmentoController::class, 'show'], true);
$router->addRoute('GET', 'admin/segmentos/delete/{id}', [SegmentoController::class, 'delete'], true);
$router->addRoute('GET', 'admin/segmentos/status/{id}', [SegmentoController::class, 'status'], true);

// Escolas
$router->addRoute('GET', 'admin/escolas/index', [EscolaController::class, 'index'], true);
$router->addRoute('GET', 'admin/escolas/adicionar', [EscolaController::class, 'create'], true);
$router->addRoute('POST', 'admin/escolas/store', [EscolaController::class, 'store'], true);
$router->addRoute('GET', 'admin/escolas/editar/{id}', [EscolaController::class, 'edit'], true);
$router->addRoute('POST', 'admin/escolas/update/{id}', [EscolaController::class, 'update'], true);
$router->addRoute('GET', 'admin/escolas/exibir/{id}', [EscolaController::class, 'show'], true);
$router->addRoute('GET', 'admin/escolas/delete/{id}', [EscolaController::class, 'delete'], true);
$router->addRoute('GET', 'admin/escolas/status/{id}', [EscolaController::class, 'status'], true);
$router->addRoute('POST', 'admin/escolas/vincular/{id}', [EscolaController::class, 'vincular'], true);

// Candidatos
$router->addRoute('GET', 'admin/candidatos/index', [CandidatoController::class, 'index'], true);
$router->addRoute('GET', 'admin/candidatos/adicionar', [CandidatoController::class, 'create'], true);
$router->addRoute('POST', 'admin/candidatos/store', [CandidatoController::class, 'store'], true);
$router->addRoute('GET', 'admin/candidatos/editar/{id}', [CandidatoController::class, 'edit'], true);
$router->addRoute('POST', 'admin/candidatos/update/{id}', [CandidatoController::class, 'update'], true);
$router->addRoute('GET', 'admin/candidatos/exibir/{id}', [CandidatoController::class, 'show'], true);
$router->addRoute('GET', 'admin/candidatos/delete/{id}', [CandidatoController::class, 'delete'], true);
$router->addRoute('GET', 'admin/candidatos/status/{id}', [CandidatoController::class, 'status'], true);

// Eleitores
$router->addRoute('GET', 'admin/eleitores/index', [EleitorController::class, 'index'], true);
$router->addRoute('GET', 'admin/eleitores/adicionar', [EleitorController::class, 'create'], true);
$router->addRoute('POST', 'admin/eleitores/store', [EleitorController::class, 'store'], true);
$router->addRoute('GET', 'admin/eleitores/editar/{id}', [EleitorController::class, 'edit'], true);
$router->addRoute('POST', 'admin/eleitores/update/{id}', [EleitorController::class, 'update'], true);
$router->addRoute('GET', 'admin/eleitores/exibir/{id}', [EleitorController::class, 'show'], true);
$router->addRoute('GET', 'admin/eleitores/delete/{id}', [EleitorController::class, 'delete'], true);
$router->addRoute('GET', 'admin/eleitores/status/{id}', [EleitorController::class, 'status'], true);

// Chapas
$router->addRoute('GET', 'admin/chapas/index', [ChapaController::class, 'index'], true);
$router->addRoute('GET', 'admin/chapas/adicionar', [ChapaController::class, 'create'], true);
$router->addRoute('POST', 'admin/chapas/store', [ChapaController::class, 'store'], true);
$router->addRoute('GET', 'admin/chapas/editar/{id}', [ChapaController::class, 'edit'], true);
$router->addRoute('POST', 'admin/chapas/update/{id}', [ChapaController::class, 'update'], true);
$router->addRoute('GET', 'admin/chapas/exibir/{id}', [ChapaController::class, 'show'], true);
$router->addRoute('GET', 'admin/chapas/delete/{id}', [ChapaController::class, 'delete'], true);
$router->addRoute('GET', 'admin/chapas/status/{id}', [ChapaController::class, 'status'], true);

// Cédulas
$router->addRoute('GET', 'admin/cedulas/index', [CedulaController::class, 'index'], true);
$router->addRoute('GET', 'admin/cedulas/adicionar', [CedulaController::class, 'create'], true);
$router->addRoute('POST', 'admin/cedulas/store', [CedulaController::class, 'store'], true);
$router->addRoute('GET', 'admin/cedulas/editar/{id}', [CedulaController::class, 'edit'], true);
$router->addRoute('POST', 'admin/cedulas/update/{id}', [CedulaController::class, 'update'], true);
$router->addRoute('GET', 'admin/cedulas/gerenciar/{id}', [CedulaController::class, 'gerenciar'], true);
$router->addRoute('GET', 'admin/cedulas/delete/{id}', [CedulaController::class, 'delete'], true);

// Resultados
$router->addRoute('GET', 'admin/resultados/index', [ResultadoController::class, 'index'], true);
$router->addRoute('GET', 'admin/resultados/adicionar', [ResultadoController::class, 'create'], true);
$router->addRoute('POST', 'admin/resultados/store', [ResultadoController::class, 'store'], true);
$router->addRoute('GET', 'admin/resultados/editar/{id}', [ResultadoController::class, 'edit'], true);
$router->addRoute('POST', 'admin/resultados/update/{id}', [ResultadoController::class, 'update'], true);
$router->addRoute('GET', 'admin/resultados/exibir/{id}', [ResultadoController::class, 'show'], true);
$router->addRoute('GET', 'admin/resultados/delete/{id}', [ResultadoController::class, 'delete'], true);

// Impressões
$router->addRoute('GET', 'admin/impressoes/cadernos', [ImpressaoController::class, 'cadernos'], true);
$router->addRoute('GET', 'admin/impressoes/cedulas', [ImpressaoController::class, 'cedulas'], true);
$router->addRoute('POST', 'admin/impressoes/impressao', [ImpressaoController::class, 'impressao'], true);

// Consultas
$router->addRoute('GET', 'admin/consultas/vendas', [ConsultaController::class, 'vendas'], true);
$router->addRoute('GET', 'admin/consultas/impressao', [ConsultaController::class, 'impressao'], true);

// Relatórios
$router->addRoute('GET', 'admin/relatorios/vendas', [RelatorioController::class, 'mensal'], true);
$router->addRoute('GET', 'admin/relatorios/impressao', [RelatorioController::class, 'impressao'], true);

$router->addRoute('POST', 'admin/configuracoes/salvar', [HomeController::class, 'salvarConfiguracoes'], true);

// Ajax
$router->addRoute('POST', 'admin/ajax/escola_segmentos', [AjaxController::class, 'escola_segmentos'], true);
