<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Cedulas</span>
            <small>Listagem de cedulas</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cedulas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <a class="btn btn-success" href="/admin/cedulas/adicionar"><i class="fa fa-plus fa-fw"></i> Gerar Cédulas</a>
    </div>
    <div class="col-md-6">
        <form method="GET" action="/admin/cedulas/index">
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
            <th>Escola</th>
            <th>Total de Cédulas</th>
            <th width="250" class="text-center">Ações</th>
        </tr>
    </thead>
    <tbody>
   
    <?php if (empty($cedulas)): ?>
    <tr>
        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
    </tr>
        <?php else: ?>
            <?php foreach ($cedulas as $cedula): ?>
                <tr>
                    <td><?= htmlspecialchars($cedula['escola_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($cedula['total_cedulas'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-center">
                        <a class="btn btn-secondary btn-sm" href="/admin/cedulas/gerenciar/<?php echo $cedula['escola_id']; ?>" title="Exibir"><i class="fa fa-cogs"></i> Gerenciar Cédulas</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
