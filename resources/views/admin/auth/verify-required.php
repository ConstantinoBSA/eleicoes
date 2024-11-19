<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>

<?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message'];
    unset($_SESSION['success_message']); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?php echo $_SESSION['error_message'];
    unset($_SESSION['error_message']); ?></p>
<?php endif; ?>

<form action="/verify-required" method="POST">
    <input type="text" value="<?php echo htmlspecialchars($email) ?>">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email) ?>">
    <button type="submit">Reenviar E-mail de Verificação</button>

    <div class="text-start">
        <a href="/login">Voltar ao Login</a>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/auth'); ?>
