<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo config()['site_name'] ?? '' ?></title>

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php __DIR__ ?>/assets/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php __DIR__ ?>/assets/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php __DIR__ ?>/assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php __DIR__ ?>/assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php __DIR__ ?>/assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php __DIR__ ?>/assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php __DIR__ ?>/assets/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php __DIR__ ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php __DIR__ ?>/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><img src="<?php __DIR__ ?>/assets/images/favicon/ms-icon-310x310.png" alt="LOGO" width="40" class="me-3"><?php echo config()['site_name'] ?? '' ?></a>
            <form class="d-flex ms-auto">
                <div class="input-group me-2">
                    <input type="search" class="form-control" placeholder="Procurar" style="width: 450px;">
                    <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fa fa-search"></i></button>
                </div>
            </form>
            <div class="dropdown ms-3">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php __DIR__ ?>/assets/images/user-perfil.png" class="rounded-circle me-2" alt="User Image" style="width: 30px;height: 30px">
                    <span class="d-none d-md-inline"><?php echo auth()->user()->name ?? '[ Usuário NI ]'; ?></span>                    
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="/admin/perfil-usuario">Perfil</a></li>
                    <li><a class="dropdown-item" href="/admin/configuracoes">Configurações</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="/admin/logout">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="main-content">
        <nav class="sidebar">
            <ul class="nav flex-column">
                <?php if (hasPermission('dashboard')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('dashboard') ?> ps-0" href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <?php endif; ?>
                <li class="nav-header"><i class="fa fa-plus fa-fw"></i> Cadastros</li>
                <?php if (hasPermission('categorias')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('categorias') ?>" href="/admin/categorias/index">- Categorias</a></li>
                <?php endif; ?>
                <?php if (hasPermission('formas_pagamento')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('formas_pagamento') ?>" href="/admin/formas_pagamento/index">- Formas de Pagamento</a></li>
                <?php endif; ?>
                <li class="nav-header"><i class="fa fa-stream fa-fw"></i> Gerenciamento</li>
                <?php if (hasPermission('clientes')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('clientes') ?>" href="/admin/clientes/index">- Clientes</a></li>
                <?php endif; ?>
                <?php if (hasPermission('produtos')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('produtos') ?>" href="/admin/produtos/index">- Produtos</a></li>
                <?php endif; ?>
                <?php if (hasPermission('kits')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('kits') ?>" href="/admin/kits/index">- Kits</a></li>
                <?php endif; ?>
                <?php if (hasPermission('vendas')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('vendas') ?>" href="/admin/vendas/index">- Vendas</a></li>
                <?php endif; ?>
                <?php if (hasPermission('pdv')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('pdv') ?>" href="/admin/pdv/index" target="_blank">- PDV</a></li>
                <?php endif; ?>
                <li class="nav-header"><i class="fa fa-satellite-dish fa-fw"></i> Site</li>
                <?php if (hasPermission('contatos')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('contatos') ?>" href="/admin/contatos/index">- Contatos</a></li>
                <?php endif; ?>
                <?php if (hasPermission('inscricoes')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('inscricoes') ?>" href="/admin/inscricoes/index">- Inscrições</a></li>
                <?php endif; ?>
                <li class="nav-header"><i class="fa fa-user-lock fa-fw"></i> Sistema</li>
                <?php if (hasPermission('permissoes')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('permissoes') ?>" href="/admin/permissoes/index">- Permissões</a></li>
                <?php endif; ?>
                <?php if (hasPermission('perfis')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('perfis') ?>" href="/admin/perfis/index">- Perfis</a></li>
                <?php endif; ?>
                <?php if (hasPermission('usuarios')): ?>
                <li class="nav-item"><a class="nav-link <?= isActiveSection('usuarios') ?>" href="/admin/usuarios/index">- Usuários</a></li>
                <?php endif; ?>
                <?php if (hasPermission('consultas')): ?>
                <li class="nav-item mt-4"><a class="nav-link <?= isActiveSection('consultas') ?> ps-0" href="/admin/consultas/vendas"><i class="fa fa-search fa-fw"></i> Consultas</a></li>
                <?php endif; ?>
                <?php if (hasPermission('relatorios')): ?>
                <li class="nav-item mt-1"><a class="nav-link <?= isActiveSection('relatorios') ?> ps-0" href="/admin/relatorios/vendas"><i class="fa fa-print fa-fw"></i> Relatórios</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <main class="content">
            <?= yieldSection('content') ?>
        </main>
    </div>

    <footer class="bg-light text-center py-3">
        <p class="mb-0">&copy; <?= date('Y') ?> Meu Aplicativo</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <!-- Toastr JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="<?php __DIR__ ?>/assets/js/script.js"></script>
    <script src="<?php __DIR__ ?>/assets/js/admin.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['message']) && isset($_SESSION['message_type'])): ?>
                toastr.<?php echo $_SESSION['message_type']; ?>("<?php echo $_SESSION['message']; ?>");
                <?php
                    unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
