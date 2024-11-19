<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<h1>Login</h1>
<form method="post" action="login" class="w-50">
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="text" name="email" class="form-control" id="email" value="joao.silva@example.com">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Senha</label>
        <input type="password" name="password" class="form-control" id="password" value="123">
    </div>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error">
            <?php
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']); // Remove a mensagem após exibi-la
        ?>
        </div>
    <?php endif; ?>

    <div class="text-end">
        <a href="/admin/forgot-password">Esqueceu sua senha?</a>
    </div>

    <hr>

    <button class="btn btn-primary" type="submit">Entrar</button>    
</form>
<?php endSection(); ?>

<?php extend('layouts/auth'); ?>
