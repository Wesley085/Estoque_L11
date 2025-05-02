{{-- @extends('admin')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    .bg-calculado {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>

<div class="container-fluid vh-100 bg-white">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <h2 class="mb-4">Editar Venda #{{ $venda->id }}</h2>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="venda_entidade_id">Entidade</label>
                                <select id="venda_entidade_id" class="form-control">
                                    <option value="">Nenhuma entidade</option>
                                    @foreach($entidades as $entidade)
                                        <option value="{{ $entidade->id }}"
                                            {{ $venda->entidade_id == $entidade->id ? 'selected' : '' }}>
                                            {{ $entidade->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="venda_imposto_percent">Imposto sobre a venda (%)</label>
                                <select id="venda_imposto_percent" class="form-control" onchange="calcularTotaisVenda()">
                                    <option value="7" {{ $venda->imposto_percent == 7 ? 'selected' : '' }}>7%</option>
                                    <option value="12" {{ $venda->imposto_percent == 12 ? 'selected' : '' }}>12%</option>
                                    <option value="15" {{ $venda->imposto_percent == 15 ? 'selected' : '' }}>15%</option>
                                    <option value="0" {{ $venda->imposto_percent == 0 ? 'selected' : '' }}>0%</option>
                                    <option value="custom" {{ !in_array($venda->imposto_percent, [7,12,15,0]) ? 'selected' : '' }}>Personalizado</option>
                                </select>
                            </div>
                            <div class="form-group" id="venda_custom_imposto_container"
                                 style="{{ !in_array($venda->imposto_percent, [7,12,15,0]) ? '' : 'display: none;' }}">
                                <input type="number" step="0.1" id="venda_custom_imposto" class="form-control"
                                       placeholder="Digite a %" oninput="calcularTotaisVenda()"
                                       value="{{ !in_array($venda->imposto_percent, [7,12,15,0]) ? $venda->imposto_percent : '' }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="venda_frete">Frete (Automático)</label>
                                <input type="number" step="0.01" id="venda_frete" class="form-control" readonly
                                       value="{{ $venda->frete }}">
                                <small class="text-muted">Frete: 2.5% do total (acima de R$ 3.000) ou R$ 65,00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="" method="post" id="form-add-produto">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="nome_produto">Produto</label>
                                            <select id="nome_produto" class="form-control" name="nome_produto" onchange="buscarProduto(this.value)">
                                                <option value="">Selecione um produto</option>
                                                @foreach($produtos as $produto)
                                                    <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="valor_compra">Valor de compra</label>
                                            <input type="number" step="0.01" name="valor_compra" id="valor_compra" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="valor_venda">Valor de venda</label>
                                            <input type="number" step="0.01" name="valor_venda" id="valor_venda" class="form-control" oninput="calcularTotais()">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="quantidade">Quantidade</label>
                                            <input type="number" step="1" min="1" name="quantidade" id="quantidade" class="form-control" oninput="calcularTotais()">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="valor_total">Valor total</label>
                                            <input type="number" step="0.01" name="valor_total" id="valor_total" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-sm-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary" onclick="submitProduto()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Produto</th>
                                    <th>Valor Compra</th>
                                    <th>Valor Venda</th>
                                    <th>Quantidade</th>
                                    <th>Lucro</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabela_produtos">
                                @foreach($produtosVenda as $produto)
                                <tr data-produto-id="{{ $produto->id }}">
                                    <td>{{ $produto->id }}</td>
                                    <td>{{ $produto->nome }}</td>
                                    <td>R$ {{ number_format($produto->pivot->valor_compra, 2) }}</td>
                                    <td><input type="number" step="0.01" class="form-control valor-venda"
                                               value="{{ $produto->pivot->valor_venda }}"
                                               data-id="{{ $produto->id }}"></td>
                                    <td><input type="number" step="1" min="1" class="form-control quantidade"
                                               value="{{ $produto->pivot->quantidade }}"
                                               data-id="{{ $produto->id }}"></td>
                                    <td class="lucro">R$ {{ number_format($produto->pivot->lucro, 2) }}</td>
                                    <td class="total-produto">R$ {{ number_format($produto->pivot->valor_total, 2) }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-remover" data-id="{{ $produto->id }}">
                                            Remover
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Imposto</th>
                                    <th class="text-right" id="valor_imposto">R$ {{ number_format($venda->imposto, 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">Frete</th>
                                    <th class="text-right" id="valor_frete">R$ {{ number_format($venda->frete, 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">Valor total</th>
                                    <th class="text-right" id="valor_total_geral">R$ {{ number_format($venda->valor_total, 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <form action="{{ route('vendas.update', $venda->id) }}" method="post" id="form-update-venda">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="produtos" id="produtos_json">
                        <input type="hidden" name="imposto" id="input_imposto" value="{{ $venda->imposto }}">
                        <input type="hidden" name="frete" id="input_frete" value="{{ $venda->frete }}">
                        <input type="hidden" name="entidade_id" id="input_entidade_id" value="{{ $venda->entidade_id }}">

                        <div class="row mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-success mr-2" onclick="atualizarVenda()">
                                    Atualizar Venda
                                </button>
                                <a href="{{ route('entidades.vendas', $venda->entidade_id) }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}














@extends('admin')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #f8f9fc;
        --accent-color: #2e59d9;
        --success-color: #1cc88a;
        --danger-color: #e74a3b;
        --warning-color: #f6c23e;
        --text-dark: #5a5c69;
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .container-fluid {
        padding: 20px;
    }

    .card {
        border: none;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: var(--secondary-color);
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.35rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    h2 {
        color: var(--text-dark);
        font-weight: 700;
        font-size: 1.75rem;
    }

    .form-control, .select2-container--default .select2-selection--single {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.375rem 0.75rem;
        height: calc(2.25rem + 2px);
    }

    .form-control:focus, .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }

    .btn-success {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }

    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .btn-warning {
        background-color: var(--warning-color);
        border-color: var(--warning-color);
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #858796;
    }

    .table th {
        background-color: var(--secondary-color);
        color: var(--text-dark);
        font-weight: 700;
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #e3e6f0;
    }

    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #e3e6f0;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .table-bordered {
        border: 1px solid #e3e6f0;
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #e3e6f0;
    }

    .bg-calculado {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .valor-venda, .quantidade {
        max-width: 100px;
    }

    .btn-remover {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn i {
        margin-right: 0.25rem;
    }

    .text-right {
        text-align: right !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .badge {
        font-weight: 600;
        padding: 0.35em 0.65em;
    }

    tfoot tr {
        background-color: var(--secondary-color);
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12 col-lg-10">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0 text-gray-800">Editar Venda #{{ $venda->id }}</h2>
                <a href="{{ route('entidades.vendas', $venda->entidade_id) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Voltar
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações da Venda</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="venda_entidade_id">Entidade</label>
                                <select id="venda_entidade_id" class="form-control select2">
                                    <option value="">Nenhuma entidade</option>
                                    @foreach($entidades as $entidade)
                                        <option value="{{ $entidade->id }}"
                                            {{ $venda->entidade_id == $entidade->id ? 'selected' : '' }}>
                                            {{ $entidade->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="venda_imposto_percent">Imposto sobre a venda (%)</label>
                                <select id="venda_imposto_percent" class="form-control" onchange="calcularTotaisVenda()">
                                    <option value="7" {{ $venda->imposto_percent == 7 ? 'selected' : '' }}>7%</option>
                                    <option value="12" {{ $venda->imposto_percent == 12 ? 'selected' : '' }}>12%</option>
                                    <option value="15" {{ $venda->imposto_percent == 15 ? 'selected' : '' }}>15%</option>
                                    <option value="0" {{ $venda->imposto_percent == 0 ? 'selected' : '' }}>0%</option>
                                    <option value="custom" {{ !in_array($venda->imposto_percent, [7,12,15,0]) ? 'selected' : '' }}>Personalizado</option>
                                </select>
                            </div>
                            <div class="form-group" id="venda_custom_imposto_container"
                                 style="{{ !in_array($venda->imposto_percent, [7,12,15,0]) ? '' : 'display: none;' }}">
                                <input type="number" step="0.1" id="venda_custom_imposto" class="form-control"
                                       placeholder="Digite a %" oninput="calcularTotaisVenda()"
                                       value="{{ !in_array($venda->imposto_percent, [7,12,15,0]) ? $venda->imposto_percent : '' }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="venda_frete">Frete (Automático)</label>
                                <input type="number" step="0.01" id="venda_frete" class="form-control bg-light" readonly
                                       value="{{ $venda->frete }}">
                                <small class="form-text text-muted">Frete: 2.5% do total (acima de R$ 3.000) ou R$ 65,00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Adicionar Produtos</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post" id="form-add-produto">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nome_produto">Produto</label>
                                    <select id="nome_produto" class="form-control select2" name="nome_produto" onchange="buscarProduto(this.value)">
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="valor_compra">Valor de compra</label>
                                    <input type="number" step="0.01" name="valor_compra" id="valor_compra" readonly class="form-control bg-light">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="valor_venda">Valor de venda</label>
                                    <input type="number" step="0.01" name="valor_venda" id="valor_venda" class="form-control" oninput="calcularTotais()">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="quantidade">Quantidade</label>
                                    <input type="number" step="1" min="1" name="quantidade" id="quantidade" class="form-control" oninput="calcularTotais()" value="1">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="valor_total">Valor total</label>
                                    <input type="number" step="0.01" name="valor_total" id="valor_total" readonly class="form-control bg-light">
                                </div>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-block" onclick="submitProduto()">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produtos da Venda</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Produto</th>
                                    <th>Valor Compra</th>
                                    <th>Valor Venda</th>
                                    <th>Quantidade</th>
                                    <th>Lucro</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabela_produtos">
                                @foreach($produtosVenda as $produto)
                                <tr data-produto-id="{{ $produto->id }}">
                                    <td>{{ $produto->id }}</td>
                                    <td>{{ $produto->nome }}</td>
                                    <td>R$ {{ number_format($produto->pivot->valor_compra, 2, ',', '.') }}</td>
                                    <td><input type="number" step="0.01" class="form-control valor-venda"
                                               value="{{ $produto->pivot->valor_venda }}"
                                               data-id="{{ $produto->id }}"></td>
                                    <td><input type="number" step="1" min="1" class="form-control quantidade"
                                               value="{{ $produto->pivot->quantidade }}"
                                               data-id="{{ $produto->id }}"></td>
                                    <td class="lucro">R$ {{ number_format($produto->pivot->lucro, 2, ',', '.') }}</td>
                                    <td class="total-produto">R$ {{ number_format($produto->pivot->valor_total, 2, ',', '.') }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm btn-remover" data-id="{{ $produto->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Imposto</th>
                                    <th class="text-right" id="valor_imposto">R$ {{ number_format($venda->imposto, 2, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">Frete</th>
                                    <th class="text-right" id="valor_frete">R$ {{ number_format($venda->frete, 2, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr class="table-active">
                                    <th colspan="5" class="text-right">Valor total</th>
                                    <th class="text-right" id="valor_total_geral">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <form action="{{ route('vendas.update', $venda->id) }}" method="post" id="form-update-venda">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="produtos" id="produtos_json">
                        <input type="hidden" name="imposto" id="input_imposto" value="{{ $venda->imposto }}">
                        <input type="hidden" name="frete" id="input_frete" value="{{ $venda->frete }}">
                        <input type="hidden" name="entidade_id" id="input_entidade_id" value="{{ $venda->entidade_id }}">

                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-success mr-2" onclick="atualizarVenda()">
                                    <i class="fas fa-save"></i> Atualizar Venda
                                </button>
                                <a href="{{ route('entidades.vendas', $venda->entidade_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap'
        });

        // Inicializa os tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Atualiza os totais ao carregar a página
        calcularTotaisVenda();
    });
</script>
@endsection --}}


<script>
    let produtosCarrinho = {!! $produtosVenda->map(function($produto) {
        return [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'valor_compra' => $produto->pivot->valor_compra,
            'valor_venda' => $produto->pivot->valor_venda,
            'quantidade' => $produto->pivot->quantidade,
            'total' => $produto->pivot->valor_total,
            'lucro' => $produto->pivot->lucro
        ];
    })->toJson() !!};

    function buscarProduto(produtoId) {
        if (produtoId) {
            $.ajax({
                url: `/produtos/${produtoId}`,
                type: 'GET',
                success: function(data) {
                    $('#valor_compra').val(data.valor_compra || data.valor);
                },
                error: function() {
                    alert('Erro ao buscar o produto');
                }
            });
        }
    }

    function calcularTotais() {
        const valorCompra = parseFloat($('#valor_compra').val()) || 0;
        const valorVenda = parseFloat($('#valor_venda').val()) || 0;
        const quantidade = parseInt($('#quantidade').val()) || 0;

        if (valorVenda > 0 && quantidade > 0) {
            // if (valorVenda < valorCompra) {
            //     alert('O valor de venda não pode ser menor que o de compra!');
            //     $('#valor_venda').val('');
            //     return false;
            // }

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

        if (produtoId && valorCompra > 0 && valorVenda >= valorCompra && quantidade > 0) {
            const totalVenda = valorVenda * quantidade;
            const lucro = totalVenda - (valorCompra * quantidade);

            // Verifica se o produto já está no carrinho
            const index = produtosCarrinho.findIndex(p => p.id == produtoId);

            if (index >= 0) {
                // Atualiza produto existente
                produtosCarrinho[index] = {
                    id: produtoId,
                    nome: produtoNome,
                    valor_compra: valorCompra,
                    valor_venda: valorVenda,
                    quantidade: quantidade,
                    total: totalVenda,
                    lucro: lucro
                };
            } else {
                // Adiciona novo produto
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
            calcularTotaisVenda();
            resetarCamposProduto();
        } else {
            alert('Preencha todos os campos corretamente!');
        }
    }

    function atualizarTabelaProdutos() {
        $('#tabela_produtos').empty();
        let subtotal = 0;

        produtosCarrinho.forEach(produto => {
            subtotal += produto.total;

            const novaLinha = `
                <tr data-produto-id="${produto.id}">
                    <td>${produto.id}</td>
                    <td>${produto.nome}</td>
                    <td>R$ ${produto.valor_compra.toFixed(2)}</td>
                    <td><input type="number" step="0.01" class="form-control valor-venda"
                               value="${produto.valor_venda.toFixed(2)}"
                               data-id="${produto.id}"></td>
                    <td><input type="number" step="1" min="1" class="form-control quantidade"
                               value="${produto.quantidade}"
                               data-id="${produto.id}"></td>
                    <td class="lucro">R$ ${produto.lucro.toFixed(2)}</td>
                    <td class="total-produto">R$ ${produto.total.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-danger btn-remover" data-id="${produto.id}">
                            Remover
                        </button>
                    </td>
                </tr>
            `;

            $('#tabela_produtos').append(novaLinha);
        });

        // Adiciona eventos aos inputs dinâmicos
        $('.valor-venda, .quantidade').on('input', function() {
            const produtoId = $(this).data('id');
            const produto = produtosCarrinho.find(p => p.id == produtoId);

            if ($(this).hasClass('valor-venda')) {
                produto.valor_venda = parseFloat($(this).val()) || 0;
            } else {
                produto.quantidade = parseInt($(this).val()) || 0;
            }

            // Recalcula totais
            produto.total = produto.valor_venda * produto.quantidade;
            produto.lucro = produto.total - (produto.valor_compra * produto.quantidade);

            // Atualiza exibição
            const row = $(this).closest('tr');
            row.find('.total-produto').text('R$ ' + produto.total.toFixed(2));
            row.find('.lucro').text('R$ ' + produto.lucro.toFixed(2));

            calcularTotaisVenda();
        });

        // Adiciona eventos aos botões de remover
        $('.btn-remover').click(function() {
            const produtoId = $(this).data('id');
            produtosCarrinho = produtosCarrinho.filter(p => p.id != produtoId);
            atualizarTabelaProdutos();
            calcularTotaisVenda();
        });

        // Atualiza subtotal
        $('#valor_total_geral').text('R$ ' + subtotal.toFixed(2));
    }

    function calcularTotaisVenda() {
        const subtotal = produtosCarrinho.reduce((sum, p) => sum + p.total, 0);
        const impostoPercent = $('#venda_imposto_percent').val() === 'custom' ?
            parseFloat($('#venda_custom_imposto').val()) || 0 :
            parseFloat($('#venda_imposto_percent').val()) || 0;

        const imposto = subtotal * (impostoPercent / 100);
        const frete = subtotal > 3000 ? subtotal * 0.025 : 65;
        const totalGeral = subtotal + imposto + frete;

        // Atualiza campos hidden para envio
        $('#input_imposto').val(imposto);
        $('#input_frete').val(frete);
        $('#input_entidade_id').val($('#venda_entidade_id').val() || null);

        // Atualiza exibição
        $('#valor_imposto').text('R$ ' + imposto.toFixed(2));
        $('#valor_frete').text('R$ ' + frete.toFixed(2));
        $('#valor_total_geral').text('R$ ' + totalGeral.toFixed(2));
        $('#venda_frete').val(frete.toFixed(2));
    }

    function atualizarVenda() {
        if (produtosCarrinho.length === 0) {
            alert('Adicione pelo menos um produto antes de atualizar!');
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
            imposto: 0, // Será calculado no backend
            frete: 0,   // Será calculado no backend
            lucro: p.lucro
        }));

        try {
            $('#produtos_json').val(JSON.stringify(produtosParaEnvio));
            document.getElementById('form-update-venda').submit();
        } catch (e) {
            console.error('Erro ao gerar JSON:', e);
            alert('Erro ao processar produtos. Verifique o console para detalhes.');
        }
    }

    function resetarCamposProduto() {
        $('#nome_produto').val('');
        $('#valor_compra').val('');
        $('#valor_venda').val('');
        $('#quantidade').val('');
        $('#valor_total').val('');
    }

    $(document).ready(function() {
        // Inicializa a tabela de produtos
        atualizarTabelaProdutos();

        $('#venda_imposto_percent').change(function() {
            if ($(this).val() === 'custom') {
                $('#venda_custom_imposto_container').show();
                $('#venda_custom_imposto').val('').focus();
            } else {
                $('#venda_custom_imposto_container').hide();
                calcularTotaisVenda();
            }
        });

        // Eventos para cálculo em tempo real
        $('#venda_custom_imposto, #venda_entidade_id').on('input change', function() {
            calcularTotaisVenda();
        });

        $('#valor_venda, #quantidade').on('input', function() {
            calcularTotais();
        });
    });
</script>
@endsection
