<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row justify-content-center text-center">
    <img src="<?php __DIR__ ?>/assets/images/logo.jpg" alt="" style="width: 20%;">

    <a class="mt-5 btn btn-dark btn-lg" href="/admin/pdv/tela">Nova Venda</a>
</div>
<?php endSection(); ?>

<?php extend('layouts/pdv'); ?>
