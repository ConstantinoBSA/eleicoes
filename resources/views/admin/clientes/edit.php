<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <h4 class="titulo-pagina mb-0">
            <span><i class="fa fa-plus fa-fw"></i> Clientes</span>
            <small>Editando cliente</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/clientes/index">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>
</div>
<small class="text-muted mb-2">Campo com (*) são obrigatório</small>

<form method="post" action="/admin/clientes/update/<?php echo $data['id']; ?>" class="mt-5">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

    <div class="row mb-3">
        <label for="nome" class="col-sm-3 col-form-label text-end text-muted">Nome: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($data['nome'] ?? ''); ?>">
            <?php if (!empty($error['nome'])): ?>
                <p class="error"><?php echo htmlspecialchars($error['nome']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="slug" class="col-sm-3 col-form-label text-end text-muted">Slug: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <textarea name="slug" class="form-control" id="slug"><?php echo htmlspecialchars($data['slug'] ?? ''); ?></textarea>
            <?php if (!empty($error['slug'])): ?>
                <p class="error"><?php echo htmlspecialchars($error['slug']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <label for="status" class="col-sm-3 col-form-label text-end text-muted">Status: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select name="status" class="form-select" id="status">
                <option value="">Selecione...</option>
                <option value="1" <?php if (($data['status'] ?? '') === 1) {
                    echo 'selected';
                } ?>>Ativo</option>
                <option value="0" <?php if (($data['status'] ?? '') === 0) {
                    echo 'selected';
                } ?>>Inativo</option>
            </select>
            <?php if (!empty($error['status'])): ?>
                <p class="error"><?php echo htmlspecialchars($error['status']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-7 offset-3 text-center">
            <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-fw"></i> Editar Cliente</button>
            <span class="mx-1">|</span>
            <a class="btn btn-secondary" href="/admin/clientes/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
        </div>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
