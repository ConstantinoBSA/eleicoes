<?php
use Picqer\Barcode\BarcodeGeneratorHTML;

$generator = new BarcodeGeneratorHTML();
?>

<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="container mt-5">
    <h1 class="mb-4">Resultados das Eleições</h1>
    <?php foreach ($resultados as $escolaNome => $dados): ?>
        <h2><?php echo htmlspecialchars($escolaNome, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p class="mb-0">Total de Eleitores: <?php echo htmlspecialchars((string)$dados['total_eleitores'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="mb-0">Total de Votos Computados: <?php echo htmlspecialchars((string)$dados['total_votos'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="mb-0">Percentual de Participação: <?php echo htmlspecialchars(number_format(($dados['total_votos'] / $dados['total_eleitores']) * 100, 2), ENT_QUOTES, 'UTF-8'); ?>%</p>
        <table class="table table-bordered mb-4">
            <thead class="thead-light">
                <tr>
                    <th>Chapa</th>
                    <th>Votos</th>
                    <th>Percentual de Votos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados['chapas'] as $chapa): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($chapa['chapa_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$chapa['votos'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(number_format($chapa['porcentagem_votos'], 2), ENT_QUOTES, 'UTF-8'); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
<?php endSection(); ?>

<?php extend('layouts/site'); ?>
