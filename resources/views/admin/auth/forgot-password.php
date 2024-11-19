<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-success">
        <?php
            echo $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Remove a mensagem após exibi-la
    ?>
    </div>
<?php endif; ?>

<form action="/forgot-password" method="POST">
    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="text" name="email" class="form-control" id="email" value="joao.silva@example.com" placeholder="Digite seu e-mail" >
    </div>

    <button class="btn btn-primary" type="submit">Enviar E-mail para Recuperação</button>

    <hr>

    <div class="text-start">
        <a href="/login">Voltar ao Login</a>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/auth'); ?>
