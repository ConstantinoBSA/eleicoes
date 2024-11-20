<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Impressões</span>
            <small>Imprimindo cédulas</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item">Impressões</li>
                <li class="breadcrumb-item active" aria-current="page">Cédulas</li>
            </ol>
        </nav>
    </div>
</div>

<form class="mt-5" action="/admin/impressoes/impressao" method="post" target="_blank">
    <div class="row mb-3">
        <label for="escola_id" class="col-sm-3 col-form-label text-end text-muted">Escola: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select class="form-select" id="escola_id" name="escola_id">
                <option value="">Selecione uma escola</option>
                <?php foreach ($escolas as $escola): ?>
                    <option value="<?php echo $escola->id; ?>" <?php echo (($candidato->escola_id ?? '') == $escola->id) ? 'selected' : ''; ?>>
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

    <div class="row mt-4">
        <div class="col-sm-6 offset-3 text-center">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-fw"></i> Imprimir Cédulas</button>
        </div>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
