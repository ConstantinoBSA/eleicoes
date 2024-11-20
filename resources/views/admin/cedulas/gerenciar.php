<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-eye fa-fw"></i> Cedulas</span>
            <small>Gerenciando cedulas</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/cedulas/index">Cedulas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gerenciar</li>
            </ol>
        </nav>
    </div>
</div>

<ul class="lista-item mt-5">
    <li><span>#ID:</span> <b><?php echo $escola->id ?></b></li>
    <li><span>Escola:</span> <b><?php echo $escola->nome ?></b></li>
    <li class="mt-3">
        <span>Status:</span> 
        <?php if ($escola->status): ?>
            <b class="badge bg-success">Ativo</b>
        <?php else: ?>
            <b class="badge bg-danger">Inativo</b>
        <?php endif; ?>
    </li>    
</ul>

<h6 class="ms-5 mt-5">- Cédulas geradas para esta escola: </h6>
<ul class="ms-5">
    <?php foreach ($cedulas as $cedula): ?>
        <li><?= htmlspecialchars($cedula->codigo_seguranca, ENT_QUOTES, 'UTF-8') ?></li>
    <?php endforeach; ?>
</ul>

<!-- Modal -->
<div class="modal fade" id="modalDelete<?php echo $cedula->id; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                <a href="/admin/cedulas/delete/<?php echo $cedula->id; ?>" class="btn btn-primary">Sim</a>
            </div>
        </div>
    </div>
</div>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
