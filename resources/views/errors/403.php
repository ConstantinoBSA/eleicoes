<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
    <h2><?php echo htmlspecialchars($errorTitle); ?></h2>
    <p><?php echo htmlspecialchars($errorMessage); ?></p>
    <a href="/admin">Voltar para a página inicial</a>
<?php endSection(); ?>

<?php extend('layouts/error'); ?>
