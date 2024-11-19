<?php
// Buffer para capturar o output
ob_start();
?>
    <h1>Consultas de Vendas</h1>
    <h2>Resumo</h2>
    <p>Aqui está o resumo das vendas para o período selecionado.</p>

    <h3>Detalhes das Vendas</h3>
    <?php if (!empty($vendas)): ?>
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td><?= htmlspecialchars($venda['id']) ?></td>
                        <td><?= htmlspecialchars($venda['cliente_nome']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($venda['data_venda']))) ?></td>
                        <td><?= htmlspecialchars('R$ ' . number_format($venda['total'], 2, ',', '.')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Sem dados de vendas disponíveis.</p>
    <?php endif; ?>
<?php
$content = ob_get_clean();

// Carregar o layout e passar o conteúdo para ele
include __DIR__ . '/../../../layouts/pdf.php';
