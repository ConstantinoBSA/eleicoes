<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<form action="/reset-password" method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="password" name="new_password" placeholder="Digite sua nova senha" required>
    <button type="submit">Redefinir Senha</button>

    <div class="text-start">
        <a href="/login">Voltar ao Login</a>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/auth'); ?>
