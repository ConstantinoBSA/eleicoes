<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row">
    <div class="col-md-6 offset-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-dashboard fa-fw"></i> Dashboard</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-3 text-center">
        <h1><?php echo $total_vendas ?></h1>
        <span class="text-muted">Total de Vendas</span>
    </div>
    <div class="col-md-3 text-center">
        <h1><?php echo $total_kits ?></h1>
        <span class="text-muted">Total de Kits</span>
    </div>
    <div class="col-md-3 text-center">
        <h1><?php echo $total_produtos ?></h1>
        <span class="text-muted">Total de Produtos</span>
    </div>
    <div class="col-md-3 text-center">
        <h1><?php echo $total_categorias ?></h1>
        <span class="text-muted">Total de Categorias</span>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12">
        <div id="grafico-vendas" style="height: 350px;"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        chart: {
            type: 'bar',
            height: 250
        },
        title: {
            text: 'Vendas por Mês - Ano <?= $anoAtual ?>',
            align: 'center' // Centraliza o título
        },
        series: [{
            name: 'Total de Vendas',
            data: <?= json_encode($dadosVendas) ?>
        }],
        xaxis: {
            categories: <?= json_encode($meses) ?>
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(value) {
                return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            },
            style: {
                fontSize: '12px',
                colors: ["#ffffff"]
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#grafico-vendas"), options);
    chart.render();
</script>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
