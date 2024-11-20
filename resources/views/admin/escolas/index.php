<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Escolas</span>
            <small>Listagem de escolas</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Escolas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/escolas/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Nova Escola</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/escolas/index">
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
            <th>Endereço</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
   
    <?php if (empty($escolas)): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($escolas as $escola): ?>
                <tr>
                    <td class="text-center">
                        <?php if ($escola->status): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $escola->id; ?></td>
                    <td><?php echo $escola->nome; ?></td>
                    <td><?php echo $escola->endereco; ?></td>
                    <td class="text-center">
                        <a class="btn btn-table text-secondary" href="/admin/escolas/exibir/<?php echo $escola->id; ?>" title="Exibir"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-table text-warning" href="/admin/escolas/editar/<?php echo $escola->id; ?>" title="Editar"><i class="fa fa-pen-to-square"></i></a>
                        <button type="button" class="btn btn-table text-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $escola->id; ?>" title="Deletar"><i class="fa fa-trash"></i></button>
                        <button type="button" class="btn btn-table text-primary" data-bs-toggle="modal" data-bs-target="#modalStatus<?php echo $escola->id; ?>" title="Status"><i class="fa fa-check-to-slot"></i></button>
                    </td>
                </tr>

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

                <!-- Modal Status -->
                <div class="modal fade" id="modalStatus<?php echo $escola->id; ?>" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
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
                                <a href="/admin/escolas/status/<?php echo $escola->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php $escolas->pagination($_GET['search'] ?? ''); ?>

<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
