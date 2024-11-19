<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
 <div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Itens da Compra</h5>
                <ul class="list-group product-list" id="cart-items">
                    <!-- Os itens adicionados aparecem aqui -->
                </ul>
                <p class="total" id="cart-total">Total: R$0,00</p>
                <button class="btn btn-danger btn-finalizar" id="clear-cart">Cancelar Compra</button>
                <button class="btn btn-success btn-finalizar" id="finalize-purchase">Finalizar Compra</button>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Adicionar Produto</h5>
                <input type="text" class="form-control mb-2" id="product-search" placeholder="Código de barras ou nome">
                <ul class="search-results" id="search-results" style="display: none;">
                    <!-- Resultados da pesquisa aparecem aqui -->
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endSection(); ?>

<?php extend('layouts/pdv'); ?>
