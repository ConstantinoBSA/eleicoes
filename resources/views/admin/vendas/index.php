<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Vendas</span>
            <small>Listagem de vendas</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Vendas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/vendas/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Nova Venda</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/vendas/index">
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
            <th>Cliente</th>
            <th>Forma de Pagamento</th>
            <th>Data da Venda</th>
            <th>Total</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($data['vendas'])): ?>
    <tr>
        <td colspan="7" class="text-center">Nenhum registro encontrado.</td>
    </tr>
    <?php else: ?>
        <?php foreach ($data['vendas'] as $venda): ?>
            <tr>
                <td class="text-center">
                    <?php if ($venda['status'] == 'pendente'): ?>
                        <span class="badge bg-danger">Pendente</span>
                    <?php else: ?>
                        <span class="badge bg-success">Concluído</span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?php echo htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($venda['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($venda['forma_pagamento_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($venda['data_venda'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars(number_format($venda['total'], 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="text-center">
                    <a class="btn btn-secondary btn-sm" href="/admin/vendas/exibir/<?php echo htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-warning btn-sm" href="/admin/vendas/editar/<?php echo htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-pencil"></i></a>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i></button>
                </td>
            </tr>

            <!-- Modal -->
            <div class="modal fade" id="modalDelete<?php echo htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                            <a href="/admin/vendas/delete/<?php echo htmlspecialchars($venda['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Sim</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-md-4">
        Mostrando de <?php echo $start; ?> até <?php echo $end; ?> de <?php echo $totalVendas; ?> registros
    </div>
    <div class="col-md-8">
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">
                        <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
