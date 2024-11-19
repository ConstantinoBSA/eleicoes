<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Clientes</span>
            <small>Listagem de clientes</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Clientes</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/clientes/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Novo Cliente</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/clientes/index">
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
            <th>Telefone</th>
            <th>E-mail</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($data['clientes'])): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($data['clientes'] as $cliente): ?>
                <tr>
                    <td class="text-center">
                        <?php if ($cliente['status'] == 'ativo'): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $cliente['id']; ?></td>
                    <td><?php echo $cliente['nome']; ?></td>
                    <td><?php echo $cliente['telefone']; ?></td>
                    <td><?php echo $cliente['email']; ?></td>
                    <td class="text-center">
                        <a class="btn btn-secondary btn-sm" href="/admin/clientes/exibir/<?php echo $cliente['id']; ?>"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-warning btn-sm" href="/admin/clientes/editar/<?php echo $cliente['id']; ?>"><i class="fa fa-pencil"></i></a>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $cliente['id']; ?>"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="modalDelete<?php echo $cliente['id']; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                                <a href="/admin/clientes/delete/<?php echo $cliente['id']; ?>" class="btn btn-primary">Sim</a>
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
        Mostrando de <?php echo $start; ?> até <?php echo $end; ?> de <?php echo $totalClientes; ?> registros
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
