<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<?php startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <h4 class="titulo-pagina mb-0">
            <span><i class="fa fa-plus fa-fw"></i> Cedulas</span>
            <small>Adicionando cedula</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/cedulas/index">Cedulas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Adicionar</li>
            </ol>
        </nav>
    </div>
</div>
<small class="text-muted mb-2">Campo com (*) são obrigatório</small>

<form method="post" action="/admin/cedulas/store" class="mt-5">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

    <div class="row mb-3">
        <label for="escola_id" class="col-sm-3 col-form-label text-end text-muted">Escola: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select class="form-select" id="escola_id" name="escola_id">
                <option value="">Selecione uma escola</option>
                <?php foreach ($escolas as $escola): ?>
                    <option value="<?php echo $escola->id; ?>" <?php echo (($cedula->escola_id ?? '') == $escola->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($escola->nome); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['escola_id'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['escola_id']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <label for="tipo" class="col-sm-3 col-form-label text-end text-muted">Tipo: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select name="tipo" class="form-select" id="tipo">
                <option value="">Selecione...</option>
                <option value="branca" <?php if (($cedula->tipo ?? '') == 'branca') {
                    echo 'selected';
                } ?>>Branca</option>
                <option value="amarela" <?php if (($cedula->tipo ?? '') == 'amarela') {
                    echo 'selected';
                } ?>>Amarela</option>
            </select>
            <?php if (!empty($errors['tipo'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['tipo']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="quantidade" class="col-sm-3 col-form-label text-end text-muted">Quantidade: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" value="<?php echo htmlspecialchars($cedula->quantidade ?? 1); ?>">
            <?php if (!empty($errors['quantidade'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['quantidade']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-7 offset-3 text-center">
            <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-fw"></i> Adicionar Cedula</button>
            <span class="mx-1">|</span>
            <a class="btn btn-secondary" href="/admin/cedulas/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
        </div>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
