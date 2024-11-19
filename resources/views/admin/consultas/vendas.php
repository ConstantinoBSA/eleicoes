<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-list fa-fw"></i> Consultas</span>
            <small>Consultando vendas</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item">Consultas</li>
                <li class="breadcrumb-item active" aria-current="page">Vendas</li>
            </ol>
        </nav>
    </div>
</div>

<form class="mt-5" action="/admin/consultas/impressao" method="post" target="_blank">
    <div class="row mb-3">
        <label for="data_inicial" class="col-sm-3 col-form-label text-end text-muted">Data Inicial:</label>
        <div class="col-sm-6">
            <input type="date" name="data_inicial" id="data_inicial" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="data_final" class="col-sm-3 col-form-label text-end text-muted">Data Final:</label>
        <div class="col-sm-6">
            <input type="date" name="data_final" id="data_final" class="form-control" required>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-6 offset-3 text-center">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-fw"></i> Gerar Relatório</button>
        </div>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
