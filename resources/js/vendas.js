// resources/js/vendas.js
$(document).ready(function() {
    // Cálculo automático quando valores mudam
    $(document).on('input', '.quantidade, .valor-venda', function() {
        const row = $(this).closest('tr');
        const quantidade = parseFloat(row.find('.quantidade').val()) || 0;
        const valorVenda = parseFloat(row.find('.valor-venda').val()) || 0;
        const valorCompra = parseFloat(row.find('.valor-compra').val()) || 0;

        const total = quantidade * valorVenda;
        const lucro = quantidade * (valorVenda - valorCompra);

        row.find('.total').text('R$ ' + total.toFixed(2).replace('.', ','));
        row.find('.lucro').text('R$ ' + lucro.toFixed(2).replace('.', ','));
    });

    // Carregar dados do produto quando selecionado
    $(document).on('change', '.produto-select', function() {
        const produtoId = $(this).val();
        const row = $(this).closest('tr');

        if (produtoId) {
            $.get(`/produtos/${produtoId}`, function(data) {
                row.find('.valor-compra').val(data.valor_compra);
                row.find('.valor-venda').val(data.valor_venda);
                row.find('.quantidade').val(1).trigger('input');
            });
        }
    });
});
