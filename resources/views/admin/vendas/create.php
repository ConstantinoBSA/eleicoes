<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <h4 class="titulo-pagina mb-0">
            <span><i class="fa fa-plus fa-fw"></i> Vendas</span>
            <small>Adicionando venda</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/vendas/index">Vendas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Adicionar</li>
            </ol>
        </nav>
    </div>
</div>
<small class="text-muted mb-2">Campo com (*) são obrigatório</small>

<form method="post" action="/admin/vendas/store" class="mt-5">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

    <!-- Informações da Venda -->
    <div class="row mb-3">
        <label for="cliente_id" class="col-sm-3 col-form-label text-end text-muted">Cliente:</label>
        <div class="col-sm-7">
            <select name="cliente_id" class="form-select" id="cliente_id">
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="forma_pagamento_id" class="col-sm-3 col-form-label text-end text-muted">Forma de Pagamento:</label>
        <div class="col-sm-7">
            <select name="forma_pagamento_id" class="form-select" id="forma_pagamento_id">
                <?php foreach ($formasPagamento as $forma): ?>
                    <option value="<?php echo $forma['id']; ?>"><?php echo htmlspecialchars($forma['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Tabela de Itens -->
    <div class="row mb-3">
        <div class="col-sm-10 offset-1">
            <table class="table table-bordered" id="items-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    <!-- Linhas de itens serão adicionadas dinamicamente aqui -->
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" onclick="addItemRow()">Adicionar Produto</button>
        </div>
    </div>

    <!-- Total Geral -->
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label text-end text-muted">Total Geral:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="total-price" readonly value="0.00">
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-7 offset-3 text-center">
            <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-fw"></i> Adicionar Venda</button>
            <span class="mx-1">|</span>
            <a class="btn btn-secondary" href="/admin/vendas/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
        </div>
    </div>
</form>

<script>
    let itemCount = <?php echo isset($_SESSION['venda_items']) ? count($_SESSION['venda_items']) : 0; ?>;
    const produtoPrecos = <?php echo json_encode(array_column($produtos, 'preco', 'id')); ?>;

    // Formatação da moeda para o Real Brasileiro
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    };

    function addItemRow(produtoId = '', quantidade = 1, preco = 0) {
        const itemsBody = document.getElementById('items-body');
        const row = document.createElement('tr');
        const selectedPreco = produtoId ? produtoPrecos[produtoId] : preco;
        row.innerHTML = `
            <td>
                <select name="items[${itemCount}][produto_id]" class="form-select" onchange="updatePrecoEtotal(this, ${itemCount})">
                    <option value="">Selecione...</option>
                    <?php foreach ($produtos as $produto): ?>
                        <option value="<?php echo $produto['id']; ?>" ${produtoId == '<?php echo $produto['id']; ?>' ? 'selected' : ''}>
                            <?php echo htmlspecialchars($produto['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="items[${itemCount}][quantidade]" class="form-control" value="${quantidade}" min="1" oninput="updateItemTotal(this, ${itemCount})"></td>
            <td><input type="text" name="items[${itemCount}][preco]" class="form-control" value="${formatCurrency(selectedPreco)}" readonly></td>
            <td><input type="text" class="form-control item-total" readonly value="${formatCurrency(quantidade * selectedPreco)}"></td>
            <td><button type="button" class="btn btn-danger" onclick="removeItemRow(this)">Remover</button></td>
        `;
        itemsBody.appendChild(row);
        itemCount++;
        updateTotalPrice();
    }

    function updatePrecoEtotal(select, index) {
        const row = select.closest('tr');
        const produtoId = select.value;
        const preco = produtoPrecos[produtoId] || 0;
        row.querySelector(`input[name="items[${index}][preco]"]`).value = formatCurrency(preco);
        updateItemTotal(row.querySelector(`input[name="items[${index}][quantidade]"]`), index);
    }

    function updateItemTotal(input, index) {
        const row = input.closest('tr');
        const quantidade = parseFloat(input.value) || 0;
        const preco = parseFloat(row.querySelector(`input[name="items[${index}][preco]"]`).value.replace('', '')) || 0;
        const total = quantidade * preco;
        row.querySelector('.item-total').value = formatCurrency(total);
        updateTotalPrice();
    }

    function updateTotalPrice() {
        let total = 0;
        const itemTotals = document.querySelectorAll('.item-total');
        itemTotals.forEach(item => {
            total += parseFloat(item.value.replace('', '')) || 0;
        });
        document.getElementById('total-price').value = formatCurrency(total);
    }

    function removeItemRow(button) {
        const row = button.closest('tr');
        row.remove();
        updateTotalPrice();
    }

    // Inicializa com uma linha de item
    <?php if (empty($_SESSION['venda_items'])): ?>
        addItemRow();
    <?php else: ?>
        <?php foreach ($_SESSION['venda_items'] as $item): ?>
            addItemRow('<?php echo $item['produto_id']; ?>', <?php echo $item['quantidade']; ?>, <?php echo $item['preco']; ?>);
        <?php endforeach; ?>
    <?php endif; ?>
</script>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
