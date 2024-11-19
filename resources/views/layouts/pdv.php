<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['app']['app_name'] ?? '[NOME_PROJETO]'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php __DIR__ ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php __DIR__ ?>/assets/css/pdv.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="header">
        <h1>Vitrine da Loja - PDV</h1>
        <span style="margin-left: -170px; margin-right: 5px">Atendente: <?php echo auth()->user()->name ?></span> | 
        <span id="current-time" class="float-right position-absolute ms-2"></span>
    </div>

    <div class="container">
        <?= yieldSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            loadCart();

            $('#product-search').on('input', function() {
                var query = $(this).val();
                if (query.length > 1) {
                    $('#search-results').css('display', 'block');
                    $.ajax({
                        url: '/admin/pdv/search_products',
                        method: 'GET',
                        data: {query: query},
                        success: function(data) {
                            $('#search-results').html(data).show();
                        }
                    });
                } else {
                    $('#search-results').css('display', 'none');
                }
            });

            $(document).on('click', '.add-to-cart', function() {
                var item = $(this).closest('li').data('item');
                $.ajax({
                    url: '/admin/pdv/add-product',
                    method: 'POST',
                    data: {product: JSON.stringify(item)},
                    dataType: 'json',
                    success: function(cart) {
                        updateCart(cart);
                    }
                });
                $('#search-results').hide();
                $('#product-search').val('');
            });

            $(document).on('change', '.quantity', function() {
                var codigoBarras = $(this).closest('li').data('item').codigo_barras;
                var quantidade = $(this).val();
                $.ajax({
                    url: '/admin/pdv/update-quantity',
                    method: 'POST',
                    data: {codigo_barras: codigoBarras, quantidade: quantidade},
                    dataType: 'json',
                    success: function(cart) {
                        updateCart(cart);
                    }
                });
            });

            $(document).on('click', '.remove-from-cart', function() {
                var codigoBarras = $(this).closest('li').data('item').codigo_barras;
                $.ajax({
                    url: '/admin/pdv/remove-product',
                    method: 'POST',
                    data: {codigo_barras: codigoBarras},
                    dataType: 'json',
                    success: function(cart) {
                        updateCart(cart);
                    }
                });
            });

            $('#clear-cart').click(function() {
                $.ajax({
                    url: '/admin/pdv/clear-cart',
                    method: 'POST',
                    dataType: 'json',
                    success: function(cart) {
                        updateCart(cart);
                    }
                });
            });

            $('#finalize-purchase').click(function() {
                $.ajax({
                    url: '/admin/pdv/finalize-purchase',
                    method: 'POST',
                    dataType: 'json',
                    success: function(cart) {
                        updateCart(cart);
                        alert('Compra finalizada com sucesso!');
                    },
                    error: function() {
                        alert('Ocorreu um erro ao processar a compra.');
                    }
                });
            });

            function loadCart() {
                $.ajax({
                    url: '/admin/pdv/get-cart',
                    method: 'GET',
                    dataType: 'json',
                    success: function(cart) {
                        updateCart(cart);
                    }
                });
            }

            function updateCart(cart) {
                $('#cart-items').empty();
                var total = 0;
                $.each(cart, function(index, item) {
                    var subtotal = item.preco * item.quantidade;
                    total += subtotal;
                    $('#cart-items').append(
                        `<li class="list-group-item d-flex justify-content-between align-items-center" data-item='${JSON.stringify(item)}'>
                            <img src="../../assets/images/imagem-300x200.jpg" alt="${item.nome}" style="width: 50px; height: auto;">
                            ${item.nome} (${item.codigo_barras}) - R$${item.preco}
                            <span>Qty: <input type="number" class="quantity" value="${item.quantidade}" min="1" style="width: 50px;"></span>
                            <span>Subtotal: R$${subtotal.toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm remove-from-cart"><i class="fa fa-times"></i></button>
                        </li>`
                    );
                });
                $('#cart-total').text('Total: R$' + total.toFixed(2));
            }
        });
    </script>

    <script>
        function updateTime() {
            const currentTimeElement = document.getElementById('current-time');
            const now = new Date();

            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!
            const year = now.getFullYear();

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            // Formato: DD/MM/YYYY - HH:MM:SS
            const formattedTime = `${day}/${month}/${year} - ${hours}:${minutes}:${seconds}`;
            
            currentTimeElement.textContent = formattedTime;
        }

        // Atualiza o relógio a cada segundo
        setInterval(updateTime, 1000);

        // Atualiza o tempo imediatamente ao carregar a página
        updateTime();
    </script>

    <script src="<?php __DIR__ ?>/assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['message']) && isset($_SESSION['message_type'])): ?>
                toastr.<?php echo $_SESSION['message_type']; ?>("<?php echo $_SESSION['message']; ?>");
                <?php
                    unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
