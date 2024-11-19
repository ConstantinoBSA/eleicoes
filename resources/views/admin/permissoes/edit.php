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
            <span><i class="fa fa-plus fa-fw"></i> Permissões</span>
            <small>Editando permissão</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/permissoes/index">Permissões</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>
</div>
<small class="text-muted mb-2">Campo com (*) são obrigatório</small>

<form method="post" action="/admin/permissoes/update/<?php echo $permissao->id ?? '' ?>" class="mt-5">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

    <div class="row mb-3">
        <label for="nome" class="col-sm-3 col-form-label text-end text-muted">Nome: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($permissao->nome ?? ''); ?>">
            <?php if (!empty($errors['nome'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['nome']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="label" class="col-sm-3 col-form-label text-end text-muted">Label: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="label" name="label" value="<?php echo htmlspecialchars($permissao->label ?? ''); ?>">
            <?php if (!empty($errors['label'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['label']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="descricao" class="col-sm-3 col-form-label text-end text-muted">Descrição: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo htmlspecialchars($permissao->descricao ?? ''); ?>">
            <?php if (!empty($errors['descricao'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['descricao']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <label for="agrupamento" class="col-sm-3 col-form-label text-end text-muted">Agrupamento:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="agrupamento" name="agrupamento" value="<?php echo htmlspecialchars($permissao->agrupamento ?? ''); ?>">
            <?php if (!empty($errors['agrupamento'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['agrupamento']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-7 offset-3 text-center">
            <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-fw"></i> Editar Permissão</button>
            <span class="mx-1">|</span>
            <a class="btn btn-secondary" href="/admin/permissoes/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
        </div>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
