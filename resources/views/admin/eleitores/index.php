<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Eleitores</span>
            <small>Listagem de eleitores</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Eleitores</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/eleitores/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Nova Eleitor</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/eleitores/index">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped table-bordered table-sm mt-4">
    <thead>
        <tr>
            <th width="100" class="text-center">Status</th>
            <th width="50" class="text-center">ID</th>
            <th>Nome</th>
            <th>Segmento</th>
            <th>Escola</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
   
    <?php if (empty($eleitores)): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($eleitores as $eleitor): ?>
                <tr>
                    <td class="text-center">
                        <?php if ($eleitor->status): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $eleitor->id; ?></td>
                    <td><?php echo $eleitor->nome; ?></td>
                    <td><?php echo $eleitor->segmento()->nome; ?></td>
                    <td><?php echo $eleitor->escola()->nome; ?></td>
                    <td class="text-center">
                        <a class="btn btn-table text-secondary" href="/admin/eleitores/exibir/<?php echo $eleitor->id; ?>" title="Exibir"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-table text-warning" href="/admin/eleitores/editar/<?php echo $eleitor->id; ?>" title="Editar"><i class="fa fa-pen-to-square"></i></a>
                        <button type="button" class="btn btn-table text-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $eleitor->id; ?>" title="Deletar"><i class="fa fa-trash"></i></button>
                        <button type="button" class="btn btn-table text-primary" data-bs-toggle="modal" data-bs-target="#modalStatus<?php echo $eleitor->id; ?>" title="Status"><i class="fa fa-check-to-slot"></i></button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="modalDelete<?php echo $eleitor->id; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                                <a href="/admin/eleitores/delete/<?php echo $eleitor->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Status -->
                <div class="modal fade" id="modalStatus<?php echo $eleitor->id; ?>" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalStatusLabel">Confirmação de mudança de status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Você deseja realmente alterar o status?</h6>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                                <a href="/admin/eleitores/status/<?php echo $eleitor->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php $eleitores->pagination($_GET['search'] ?? ''); ?>

<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
