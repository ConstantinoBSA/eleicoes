<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-user fa-fw"></i> Perfil do Usuário</span>
            <small>Informações do usuário</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
				<li class="breadcrumb-item active" aria-current="page">Perfil do Usuário</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-5">
    <form method="POST" action="/update-password">
        <div class="row mb-3">
            <label for="current_password" class="col-sm-3 col-form-label text-end text-muted">Senha Atual:</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="new_password" class="col-sm-3 col-form-label text-end text-muted">Nova Senha:</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="confirm_password" class="col-sm-3 col-form-label text-end text-muted">Confirme a Nova Senha:</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
        </div>

        <div class="row mb-3 mt-5">
            <div class="col-sm-6 offset-3 text-center">
                <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-fw"></i> Alterar Senha</button>
            </div>
        </div>
    </form>
</div>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
