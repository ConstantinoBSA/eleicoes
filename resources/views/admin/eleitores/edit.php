<?php

use App\Models\EscolaSegmento;

 startSection('title'); ?>
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
            <span><i class="fa fa-plus fa-fw"></i> Eleitores</span>
            <small>Editando eleitor</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/eleitores/index">Eleitores</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>
</div>
<small class="text-muted mb-2">Campo com (*) são obrigatório</small>

<form method="post" action="/admin/eleitores/update/<?php echo $eleitor->id ?? '' ?>" class="mt-5">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

    <div class="row mb-3">
        <label for="nome" class="col-sm-3 col-form-label text-end text-muted">Nome: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($eleitor->nome ?? ''); ?>">
            <?php if (!empty($errors['nome'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['nome']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="documento" class="col-sm-3 col-form-label text-end text-muted">Documento: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="documento" name="documento" value="<?php echo htmlspecialchars($eleitor->documento ?? ''); ?>">
            <?php if (!empty($errors['documento'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['documento']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="escola_id" class="col-sm-3 col-form-label text-end text-muted">Escola: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select class="form-select" id="escola_id" name="escola_id">
                <option value="">Selecione uma escola</option>
                <?php foreach ($escolas as $escola): ?>
                    <option value="<?php echo $escola->id; ?>" <?php echo (($eleitor->escola_id ?? '') == $escola->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($escola->nome); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['escola_id'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['escola_id']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-3">
        <label for="segmento_id" class="col-sm-3 col-form-label text-end text-muted">Segmento: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select class="form-select" id="segmento_id" name="segmento_id">   
                <?php if($eleitor->escola_id): ?>                    
                    <?php
                        $segmentoModel = new EscolaSegmento();
                        $segmentos = $segmentoModel
                            ->where('escola_id', $eleitor->escola_id)
                            ->get();
                    ?>

                    <?php if(count($segmentos) > 0): ?>
                        <option value="">Selecione...</option>    
                        <?php foreach ($segmentos as $segmento): ?>
                            <option value="<?php echo $segmento->segmento()->id; ?>" <?php echo (($eleitor->segmento_id ?? '') == $segmento->segmento()->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($segmento->segmento()->nome); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="">...</option>    
                    <?php endif ?>
                <?php else: ?>
                    <option value="">...</option>    
                <?php endif ?>
            </select>
            <?php if (!empty($errors['segmento_id'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['segmento_id']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <label for="status" class="col-sm-3 col-form-label text-end text-muted">Status: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select name="status" class="form-select" id="status">
                <option value="">Selecione...</option>
                <option value="1" <?php if (($eleitor->status ?? '') == 1) {
                    echo 'selected';
                } ?>>Ativo</option>
                <option value="0" <?php if (($eleitor->status ?? '') == 0) {
                    echo 'selected';
                } ?>>Inativo</option>
            </select>
            <?php if (!empty($errors['status'])): ?>
                <p class="error mb-0"><?php echo htmlspecialchars($errors['status']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-7 offset-3 text-center">
            <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-fw"></i> Editar Eleitor</button>
            <span class="mx-1">|</span>
            <a class="btn btn-secondary" href="/admin/eleitores/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#escola_id').change(function() {
        var escolaId = $(this).val();        

        if (escolaId != '') {
            $.ajax({
                url: '/admin/ajax/escola_segmentos',
                data: {
                    'id': escolaId
                },
                type: 'POST',
                success: function(data) {
                    $('#segmento_id').html(data);                    
                },
                error: function() {
                    console.log('Erro ao buscar segmentos');
                }
            });
        }else{
            $('#segmento_id').html('');      
        }
    });
});
</script>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
