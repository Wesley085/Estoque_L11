@extends('admin')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@include('mensagens.mensagem')

<style>
    .bg-calculado {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .card-produto {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .card-produto:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table thead th {
        background-color: #343a40;
        color: white;
        position: sticky;
        top: 0;
    }

    .btn-action {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .total-row {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    .valor-destaque {
        font-size: 1.1rem;
        color: #28a745;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 15px;
        }

        .table th, .table td {
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Card de Adição de Produtos -->
    <div class="card card-produto mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Adicionar Produtos</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ session('status') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="" method="post" id="form-add-produto">
                @csrf
                <div class="row">
                    <!-- Produto -->
                    <div class="col-md-4 col-lg-3">
                        <div class="form-group">
                            <label for="nome_produto">Produto</label>
                            <select id="nome_produto" class="form-control" name="nome_produto" onchange="buscarProduto(this.value)">
                                <option value="">Selecione um produto</option>
                                @foreach($produtos as $produto)
                                    <option value="{{ $produto->id }}" data-valor="{{ $produto->valor_compra ?? $produto->valor }}">{{ $produto->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Valor Compra -->
                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <label for="valor_compra">Valor de compra</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input type="number" step="0.01" name="valor_compra" id="valor_compra" readonly class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Valor Venda -->
                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <label for="valor_venda">Valor de venda</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input type="number" step="0.01" name="valor_venda" id="valor_venda" class="form-control" oninput="calcularTotais()">
                            </div>
                        </div>
                    </div>

                    <!-- Quantidade -->
                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <label for="quantidade">Quantidade</label>
                            <input type="number" step="1" min="1" name="quantidade" id="quantidade" class="form-control" value="1" oninput="calcularTotais()">
                        </div>
                    </div>

                    <!-- Valor Total -->
                    <div class="col-md-2 col-lg-2">
                        <div class="form-group">
                            <label for="valor_total">Valor total</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input type="number" step="0.01" name="valor_total" id="valor_total" readonly class="form-control bg-light">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-primary adicionar_produto" onclick="submitProduto()">
                            <i class="fas fa-plus-circle mr-1"></i> Adicionar Produto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Configurações da Venda -->
    <div class="card card-produto mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Configurações da Venda</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Entidade -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="venda_entidade_id">Cliente/Entidade</label>
                        <select id="venda_entidade_id" class="form-control">
                            <option value="">Nenhuma entidade</option>
                            @foreach($entidades as $entidade)
                                <option value="{{ $entidade->id }}">{{ $entidade->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Imposto -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="venda_imposto_percent">Imposto sobre a venda</label>
                        <div class="input-group">
                            <select id="venda_imposto_percent" class="form-control" onchange="calcularTotaisVenda()">
                                <option value="7">7%</option>
                                <option value="12">12%</option>
                                <option value="15">15%</option>
                                <option value="0">Isento</option>
                                <option value="custom">Personalizado</option>
                            </select>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="venda_custom_imposto_container" style="display: none;">
                        <div class="input-group">
                            <input type="number" step="0.1" id="venda_custom_imposto" class="form-control" placeholder="Digite a %" oninput="calcularTotaisVenda()">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frete -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="venda_frete">Frete</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">R$</span>
                            </div>
                            <input type="number" step="0.01" id="venda_frete" class="form-control" readonly>
                        </div>
                        <small class="text-muted">Calculado automaticamente: 2.5% do total (acima de R$ 3.000) ou R$ 65,00</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card card-produto mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Produtos Adicionados</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th>Produto</th>
                            <th width="12%">Compra (R$)</th>
                            <th width="12%">Venda (R$)</th>
                            <th width="10%">Qtd.</th>
                            <th width="12%">Lucro (R$)</th>
                            <th width="12%">Total (R$)</th>
                            <th width="8%">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela_produtos">
                        <!-- Produtos serão adicionados dinamicamente aqui -->
                    </tbody>
                    <tfoot class="total-row">
                        <tr>
                            <th colspan="5" class="text-right">Subtotal:</th>
                            <th id="valor_lucro_total" class="text-right">R$ 0,00</th>
                            <th id="valor_subtotal" class="text-right">R$ 0,00</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Imposto:</th>
                            <th></th>
                            <th id="valor_imposto" class="text-right">R$ 0,00</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-right">Frete:</th>
                            <th></th>
                            <th id="valor_frete" class="text-right">R$ 0,00</th>
                            <th></th>
                        </tr>
                        <tr class="bg-light">
                            <th colspan="5" class="text-right">Total Geral:</th>
                            <th></th>
                            <th id="valor_total_geral" class="text-right valor-destaque">R$ 0,00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Finalização da Venda -->
    <div class="row">
        <div class="col-12 text-right">
            <form action="{{ route('vendas.store') }}" method="post" id="finaliza" name="finaliza">
                @csrf
                <input type="hidden" name="produtos" id="produtos_json">
                <input type="hidden" name="imposto" id="input_imposto">
                <input type="hidden" name="frete" id="input_frete">
                <input type="hidden" name="entidade_id" id="input_entidade_id">

                <button type="button" class="btn btn-success btn-lg" onclick="finalizarVenda()">
                    <i class="fas fa-check-circle mr-2"></i> Confirmar Venda
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let produtosCarrinho = [];

    function buscarProduto(produtoId) {
        if (produtoId) {
            const produtoSelecionado = $('#nome_produto option:selected');
            const valorCompra = parseFloat(produtoSelecionado.data('valor')) || 0;

            if (valorCompra > 0) {
                $('#valor_compra').val(valorCompra.toFixed(2));
                $('#valor_venda').val('').focus();
                $('#quantidade').val(1);
                $('.adicionar_produto').attr('disabled', false);
            } else {
                alert('Produto com valor de compra inválido ou não encontrado!');
                resetarCamposProduto();
            }
        } else {
            resetarCamposProduto();
        }
    }

    function calcularTotais() {
        const valorCompra = parseFloat($('#valor_compra').val()) || 0;
        const valorVenda = parseFloat($('#valor_venda').val()) || 0;
        const quantidade = parseInt($('#quantidade').val()) || 0;

        if (valorCompra <= 0) {
            return false;
        }

        if (valorVenda > 0 && quantidade > 0) {
            const totalVenda = valorVenda * quantidade;
            $('#valor_total').val(totalVenda.toFixed(2));
            return true;
        }
        return false;
    }

    function submitProduto() {
        const produtoId = $('#nome_produto').val();
        const produtoNome = $('#nome_produto option:selected').text();
        const valorCompra = parseFloat($('#valor_compra').val()) || 0;
        const valorVenda = parseFloat($('#valor_venda').val()) || 0;
        const quantidade = parseInt($('#quantidade').val()) || 0;

        if (produtoId && valorCompra > 0 && valorVenda > 0 && quantidade > 0) {
            const totalVenda = valorVenda * quantidade;
            const lucro = totalVenda - (valorCompra * quantidade);

            const produtoExistente = produtosCarrinho.find(p => p.id == produtoId);

            if (produtoExistente) {
                produtoExistente.valor_venda = valorVenda;
                produtoExistente.quantidade += quantidade;
                produtoExistente.total = produtoExistente.valor_venda * produtoExistente.quantidade;
                produtoExistente.lucro = produtoExistente.total - (valorCompra * produtoExistente.quantidade);
            } else {
                produtosCarrinho.push({
                    id: produtoId,
                    nome: produtoNome,
                    valor_compra: valorCompra,
                    valor_venda: valorVenda,
                    quantidade: quantidade,
                    total: totalVenda,
                    lucro: lucro
                });
            }

            atualizarTabelaProdutos();
            resetarCamposProduto();
            $('#nome_produto').focus();
        } else {
            alert('Preencha todos os campos corretamente!');
        }
    }

    function atualizarTabelaProdutos() {
        $('#tabela_produtos').empty();
        let subtotal = 0;
        let lucroTotal = 0;

        produtosCarrinho.forEach(produto => {
            subtotal += produto.total;
            lucroTotal += produto.lucro;

            const novaLinha = `
                <tr data-produto-id="${produto.id}">
                    <td>${produto.id}</td>
                    <td>${produto.nome}</td>
                    <td class="text-right">${produto.valor_compra.toFixed(2)}</td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm valor-venda-input text-right"
                               value="${produto.valor_venda.toFixed(2)}"
                               data-id="${produto.id}">
                    </td>
                    <td>
                        <input type="number" step="1" min="1" class="form-control form-control-sm quantidade-input text-right"
                               value="${produto.quantidade}"
                               data-id="${produto.id}">
                    </td>
                    <td class="text-right lucro-cell">${produto.lucro.toFixed(2)}</td>
                    <td class="text-right total-cell">${produto.total.toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm btn-action" onclick="removerProduto(${produto.id})" title="Remover">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#tabela_produtos').append(novaLinha);
        });

        $('.valor-venda-input, .quantidade-input').on('input', function() {
            const produtoId = $(this).data('id');
            const produto = produtosCarrinho.find(p => p.id == produtoId);

            if ($(this).hasClass('valor-venda-input')) {
                const novoValor = parseFloat($(this).val()) || 0;
                produto.valor_venda = novoValor;
            } else {
                produto.quantidade = parseInt($(this).val()) || 1;
            }

            produto.total = produto.valor_venda * produto.quantidade;
            produto.lucro = produto.total - (produto.valor_compra * produto.quantidade);

            const row = $(this).closest('tr');
            row.find('.total-cell').text(produto.total.toFixed(2));
            row.find('.lucro-cell').text(produto.lucro.toFixed(2));

            calcularTotaisVenda();
        });

        $('#valor_subtotal').text(subtotal.toFixed(2));
        $('#valor_lucro_total').text(lucroTotal.toFixed(2));
        calcularTotaisVenda();
    }

    function calcularTotaisVenda() {
        const subtotal = produtosCarrinho.reduce((sum, p) => sum + p.total, 0);
        const lucroTotal = produtosCarrinho.reduce((sum, p) => sum + p.lucro, 0);
        const impostoPercent = $('#venda_imposto_percent').val() === 'custom' ?
            parseFloat($('#venda_custom_imposto').val()) || 0 :
            parseFloat($('#venda_imposto_percent').val()) || 0;

        const imposto = subtotal * (impostoPercent / 100);
        const frete = subtotal > 3000 ? subtotal * 0.025 : 65;
        const totalGeral = subtotal + imposto + frete;

        $('#input_imposto').val(imposto);
        $('#input_frete').val(frete);
        $('#input_entidade_id').val($('#venda_entidade_id').val() || null);

        $('#venda_frete').val(frete.toFixed(2));
        $('#valor_imposto').html(`${impostoPercent}% - ${imposto.toFixed(2)}`);
        $('#valor_frete').text(frete.toFixed(2));
        $('#valor_total_geral').text(totalGeral.toFixed(2));
    }

    function removerProduto(produtoId) {
        produtosCarrinho = produtosCarrinho.filter(p => p.id != produtoId);
        atualizarTabelaProdutos();
    }

    function finalizarVenda() {
        if (produtosCarrinho.length === 0) {
            alert('Adicione pelo menos um produto antes de finalizar!');
            return;
        }

        const produtosParaEnvio = produtosCarrinho.map(p => ({
            id: p.id,
            valor_compra: p.valor_compra,
            valor_venda: p.valor_venda,
            quantidade: p.quantidade,
            imposto_percent: $('#venda_imposto_percent').val() === 'custom' ?
                parseFloat($('#venda_custom_imposto').val()) || 0 :
                parseFloat($('#venda_imposto_percent').val()) || 0,
            imposto: 0,
            frete: 0,
            lucro: p.lucro
        }));

        try {
            $('#produtos_json').val(JSON.stringify(produtosParaEnvio));
            document.getElementById('finaliza').submit();
        } catch (e) {
            console.error('Erro ao gerar JSON:', e);
            alert('Erro ao processar produtos. Verifique o console para detalhes.');
        }
    }

    function resetarCamposProduto() {
        $('#valor_compra').val('');
        $('#valor_venda').val('');
        $('#quantidade').val('1');
        $('#valor_total').val('');
    }

    $(document).ready(function() {
        $('#nome_produto').focus();

        $('#venda_imposto_percent').change(function() {
            if ($(this).val() === 'custom') {
                $('#venda_custom_imposto_container').show();
                $('#venda_custom_imposto').val('').focus();
            } else {
                $('#venda_custom_imposto_container').hide();
                calcularTotaisVenda();
            }
        });

        $('#venda_custom_imposto, #venda_frete, #venda_entidade_id').on('input change', function() {
            calcularTotaisVenda();
        });

        $('#valor_venda, #quantidade').on('input', function() {
            calcularTotais();
        });
    });
</script>

<style>
    .table-danger {
        background-color: #f8d7da !important;
        animation: blink 1s step-end infinite;
    }

    @keyframes blink {
        50% { opacity: 0.7; }
    }
</style>
@endsection
