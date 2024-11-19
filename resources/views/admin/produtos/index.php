<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Produtos</span>
            <small>Listagem de produtos</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produtos</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/produtos/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Nova Produto</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/produtos/index">
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
            <th>Código</th>
            <th>Nome</th>
            <th>Slug</th>
            <th>Categoria</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($produtos)): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td class="text-center">
                        <?php if ($produto->status == 'pendente'): ?>
                            <span class="badge bg-danger">Pendente</span>
                        <?php else: ?>
                            <span class="badge bg-success">Concluído</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $produto->id; ?></td>
                    <td><?php echo $produto->codigo; ?></td>
                    <td><?php echo $produto->nome; ?></td>
                    <td><?php echo $produto->slug; ?></td>
                    <td><?php echo $produto->categoria()->nome; ?></td>
                    <td class="text-center">
                        <a class="btn btn-secondary btn-sm" href="/admin/produtos/exibir/<?php echo $produto->id; ?>"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-warning btn-sm" href="/admin/produtos/editar/<?php echo $produto->id; ?>"><i class="fa fa-pencil"></i></a>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $produto->id; ?>"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="modalDelete<?php echo $produto->id; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                                <a href="/admin/produtos/delete/<?php echo $produto->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php $produtos->pagination($_GET['search'] ?? ''); ?>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
