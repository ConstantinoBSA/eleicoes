<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtém a URL atual, excluindo a parte do protocolo e domínio
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Cria uma função para verificar se o link está ativo
function isActive($linkPattern, $currentPath)
{
    // Verifica se o início do caminho atual corresponde ao padrão fornecido
    return strpos($currentPath, $linkPattern) === 0 ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['app']['app_name'] ?? '[NOME_PROJETO]'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php __DIR__ ?>/assets/css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <?= yieldSection('content') ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <!-- Toastr JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="<?php __DIR__ ?>/assets/js/script.js"></script>
    <script src="<?php __DIR__ ?>/assets/js/site.js"></script>
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
