<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Categorias</span>
            <small>Listagem de categorias</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categorias</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/categorias/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Nova Categoria</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/categorias/index">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div>
<?php
// Verifica se a variável é do tipo correto e está populada
// if ($categorias instanceof App\Core\PaginatedCollection) {
//     echo "A variável é uma instância de PaginatedCollection.<br>";
//     var_dump($categorias);
// } else {
//     echo "A variável não é uma instância de PaginatedCollection ou está vazia.<br>";
// }

// if (!empty($categorias)) {
//     echo "Categorias não está vazia.<br>";
// } else {
//     echo "Categorias está vazia.<br>";
// }

// // Tentar acessar diretamente os itens
// if (isset($categorias[0])) {
//     echo "Primeiro item: " . $categorias[0]->nome;
// } else {
//     echo "Nenhum item encontrado na coleção.";
//     exit;
// }

// foreach ($categorias as $categoria) {
//     var_dump($categoria); // Isso deve mostrar cada categoria
//     break; // Pare após a primeira iteração para não poluir a saída
// }
?>
</div>

<table class="table table-striped table-bordered table-sm mt-4">
    <thead>
        <tr>
            <th width="100" class="text-center">Status</th>
            <th width="50" class="text-center">ID</th>
            <th>Nome</th>
            <th>Slug</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
   
    <?php if (empty($categorias)): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td class="text-center">
                        <?php if ($categoria->status): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $categoria->id; ?></td>
                    <td><?php echo $categoria->nome; ?></td>
                    <td><?php echo $categoria->slug; ?></td>
                    <td class="text-center">
                        <a class="btn btn-table text-secondary" href="/admin/categorias/exibir/<?php echo $categoria->id; ?>" title="Exibir"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-table text-warning" href="/admin/categorias/editar/<?php echo $categoria->id; ?>" title="Editar"><i class="fa fa-pen-to-square"></i></a>
                        <button type="button" class="btn btn-table text-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $categoria->id; ?>" title="Deletar"><i class="fa fa-trash"></i></button>
                        <button type="button" class="btn btn-table text-primary" data-bs-toggle="modal" data-bs-target="#modalStatus<?php echo $categoria->id; ?>" title="Status"><i class="fa fa-check-to-slot"></i></button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="modalDelete<?php echo $categoria->id; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                                <a href="/admin/categorias/delete/<?php echo $categoria->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Status -->
                <div class="modal fade" id="modalStatus<?php echo $categoria->id; ?>" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
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
                                <a href="/admin/categorias/status/<?php echo $categoria->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php $categorias->pagination($_GET['search'] ?? ''); ?>

<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
