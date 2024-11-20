<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-eye fa-fw"></i> Escolas</span>
            <small>Exibindo escola</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/escolas/index">Escolas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Exibir</li>
            </ol>
        </nav>
    </div>
</div>

<ul class="lista-item mt-5">
    <li><span>#ID:</span> <b><?php echo $escola->id ?></b></li>
    <li><span>Nome:</span> <b><?php echo $escola->nome ?></b></li>
    <li><span>Endereço:</span> <b><?php echo $escola->endereco ?></b></li>
    <li class="mt-3">
        <span>Status:</span> 
        <?php if ($escola->status): ?>
            <b class="badge bg-success">Ativo</b>
        <?php else: ?>
            <b class="badge bg-danger">Inativo</b>
        <?php endif; ?>
    </li>
    <li class="mt-3">
        <span>Ações:</span>
        <a class="btn btn-warning" href="/admin/escolas/editar/<?php echo $escola->id; ?>"><i class="fa fa-pencil fa-fw"></i> Editar Escola</a>
        <button type="button" class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $escola->id; ?>"><i class="fa fa-trash fa-fw"></i> Deletar Escola</button>
        <a class="btn btn-secondary ms-1" href="/admin/escolas/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
    </li>
</ul>

<!-- Modal -->
<div class="modal fade" id="modalDelete<?php echo $escola->id; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Confirmação de exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Você deseja deletar este registro?</h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                <a href="/admin/escolas/delete/<?php echo $escola->id; ?>" class="btn btn-primary">Sim</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <h5>- Segmentos Vinculados:</h5>
    </div>
</div>

<form action="/admin/escolas/vincular/<?php echo $escola->id ?? '' ?>" method="POST" class="ms-5 mt-3">
    <?php foreach ($segmentos as $segmento): ?>
        <div>
            <input type="checkbox" name="segmentos[]" value="<?php echo $segmento->id; ?>" 
                id="segmento_<?php echo $segmento->id ?>"
                <?php echo in_array($segmento->id, $segmentosVinculados) ? 'checked' : ''; ?>>
            <label for="segmento_<?php echo $segmento->id; ?>"><?php echo $segmento->nome; ?></label>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary mt-4"><i class="fa fa-check fa-fw"></i> Vincular Segmentos</button>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
