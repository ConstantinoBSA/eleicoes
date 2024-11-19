<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Perfis</span>
            <small>Listagem de perfis</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Perfis</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/perfis/adicionar"><i class="fa fa-plus fa-fw"></i> Criar Nova Perfil</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/perfis/index">
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
            <th width="50" class="text-center">ID</th>
            <th>Nome</th>
            <th>Label</th>
            <th>Descrição</th>
            <th width="140" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($perfis)): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($perfis as $perfil): ?>
                <tr>
                    <td class="text-center"><?php echo $perfil->id; ?></td>
                    <td><?php echo $perfil->nome; ?></td>
                    <td><?php echo $perfil->label; ?></td>
                    <td><?php echo $perfil->descricao; ?></td>
                    <td class="text-center">
                        <a class="btn btn-table text-secondary" href="/admin/perfis/exibir/<?php echo $perfil->id; ?>" title="Exibir"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-table text-warning" href="/admin/perfis/editar/<?php echo $perfil->id; ?>" title="Editar"><i class="fa fa-pen-to-square"></i></a>
                        <button type="button" class="btn btn-table text-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $perfil->id; ?>" title="Deletar"><i class="fa fa-trash"></i></button>
                        <button type="button" class="btn btn-table text-success" data-bs-toggle="modal" data-bs-target="#modalPermissoes<?php echo $perfil->id; ?>" title="Permissões"><i class="fa fa-lock"></i></button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="modalDelete<?php echo $perfil->id; ?>" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
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
                                <a href="/admin/perfis/delete/<?php echo $perfil->id; ?>" class="btn btn-primary">Sim</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Permissões -->
                <div class="modal fade" id="modalPermissoes<?php echo $perfil->id; ?>" tabindex="-1" aria-labelledby="modalPermissoesLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalPermissoesLabel">Perfil: <?php echo $perfil->label; ?></h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="marcar-todos-<?php echo $perfil->id; ?>">
                                    <label class="form-check-label me-4" for="marcar-todos-<?php echo $perfil->id; ?>">
                                        Marcar todos
                                    </label>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            </div>
                            <div class="modal-body">
                                <?php foreach ($permissoesAgrupadas as $grupo => $permissoes): ?>
                                    <h6 class="mt-3"><b>- <?php echo htmlspecialchars($grupo, ENT_QUOTES, 'UTF-8'); ?></b></h6>
                                    <div class="row ms-1">
                                        <?php foreach ($permissoes as $permissao): ?>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox"
                                                        type="checkbox"
                                                        value="<?php echo htmlspecialchars($permissao['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        id="permissao_<?php echo $perfil->id; ?><?php echo htmlspecialchars($permissao['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="permissoes[]"
                                                        <?php echo in_array($permissao['nome'], $perfil->permissoes) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="permissao_<?php echo $perfil->id; ?><?php echo htmlspecialchars($permissao['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($permissao['label'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
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
        Mostrando de <?php echo $start; ?> até <?php echo $end; ?> de <?php echo $totalPerfis; ?> registros
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
     // Ao mudar o estado do checkbox "Marcar todos"
     $('[id^=marcar-todos-]').change(function() {
        var perfilId = $(this).attr('id').replace('marcar-todos-', '');
        var isChecked = $(this).is(':checked');
        
        // Marca ou desmarca todos os checkboxes de permissão dentro da mesma modal
        $('#modalPermissoes' + perfilId + ' .permission-checkbox').prop('checked', isChecked).change();
    });
    
    // Manter a funcionalidade AJAX existente
    $('.permission-checkbox').change(function() {
        var isChecked = $(this).is(':checked');
        var permissoes = $(this).val();
        var perfilId = $(this).closest('.modal').attr('id').replace('modalPermissoes', '');

        $.ajax({
            url: '/admin/perfis/permissoes',
            method: 'POST',
            data: {
                permissoes: permissoes,
                perfil_id: perfilId,
                status: isChecked ? 'grant' : 'revoke'
            },
            dataType: 'json', // Especifica que a resposta deve ser JSON
            success: function(response) {
                if (response && response.message) {
                    console.log(response.message);
                } else {
                    console.error('Resposta inesperada:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro ao salvar permissão:', xhr.responseText || error);
            }
        });
    });
});
</script>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
