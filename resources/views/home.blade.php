@extends('admin')
@section('content')
@include('mensagens.mensagem')

<style>
    .card-dashboard {
        border-radius: 10px;
        transition: transform 0.3s ease;
        height: 100%;
    }
    .card-dashboard:hover {
        transform: translateY(-5px);
    }
    .card-dashboard img {
        filter: brightness(0) invert(1);
    }

    /* Estilos da tabela */
    .table-container {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table {
        margin-bottom: 0;
        width: 100%;
    }
    .table thead {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
    }
    .table th {
        padding: 15px 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
    }
    .table td {
        padding: 12px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    .table-hover tbody tr:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Cores para valores */
    .text-profit {
        color: #28a745;
        font-weight: 600;
    }
    .text-loss {
        color: #dc3545;
        font-weight: 600;
    }

    /* Paginação */
    /* .pagination .page-item.active .page-link {
        background-color: #000aca;
        border-color: #6c757d;
    }
    .pagination .page-link {
        color: #92ccff;
    } */

    /* Responsividade */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }
        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }
        .table tr {
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .table td {
            text-align: right;
            padding-left: 50%;
            position: relative;
            border-bottom: 1px solid #f0f0f0;
        }
        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 12px;
            width: 45%;
            padding-right: 15px;
            font-weight: 600;
            text-align: left;
            color: #6c757d;
        }
    }
</style>

<div class="container mt-4 mb-4">
    <!-- Cards Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-dashboard bg-primary text-white p-3">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('img/cart.svg') }}" alt="" height="40px" width="40px" class="me-3">
                    <div>
                        <h5 class="mb-0">{{ $vendasTotais }}</h5>
                        <small>Vendas este mês</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-dashboard bg-success text-white p-3">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('img/money.svg') }}" alt="" height="40px" width="40px" class="me-3">
                    <div>
                        <h5 class="mb-0">R$ {{ number_format($totais['faturado'], 2, ',', '.') }}</h5>
                        <small>Faturado este mês</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-dashboard bg-warning text-white p-3">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('img/product.svg') }}" alt="" height="40px" width="40px" class="me-3">
                    <div>
                        <h5 class="mb-0">{{ $totalProdutosVendidos }}</h5>
                        <small>Produtos vendidos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-dashboard bg-danger text-white p-3">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('img/stock.svg') }}" alt="" height="40px" width="40px" class="me-3">
                    <div>
                        <h5 class="mb-0">{{ $estoqueBaixo }}</h5>
                        <small>Produtos com baixo estoque</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Vendas Recentes -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Produto</th>
                        <th width="13%" class="text-center">Quantidade</th>
                        <th width="12%" class="text-end">Compra (R$)</th>
                        <th width="12%" class="text-end">Venda (R$)</th>
                        <th width="13%" class="text-end">Total Compra</th>
                        <th width="13%" class="text-end">Total Venda</th>
                        <th width="12%" class="text-end">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vendas as $venda)
                    @php
                        $lucro = $venda->valor_total - ($venda->valor_compra * $venda->quantidade);
                    @endphp
                    <tr>
                        <td data-label="ID">{{ $venda->venda->id ?? 'N/A' }}</td>
                        <td data-label="Produto">{{ $venda->produto->nome ?? 'Produto Removido' }}</td>
                        <td data-label="Quantidade" class="text-end">{{ $venda->quantidade }}</td>
                        <td data-label="Compra (R$)" class="text-end">R$ {{ number_format($venda->valor_compra, 2, ',', '.') }}</td>
                        <td data-label="Venda (R$)" class="text-end">R$ {{ number_format($venda->valor_venda, 2, ',', '.') }}</td>
                        <td data-label="Total Compra" class="text-end">R$ {{ number_format($venda->valor_compra * $venda->quantidade, 2, ',', '.') }}</td>
                        <td data-label="Total Venda" class="text-end {{ $lucro >= 0 ? 'text-profit' : 'text-loss' }}">
                            R$ {{ number_format($venda->valor_total, 2, ',', '.') }}
                        </td>
                        <td data-label="Data" class="text-end">{{ $venda->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Nenhuma venda registrada no período</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    @if($vendas->hasPages())
    <div class="mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                {{-- Previous Page Link --}}
                @if ($vendas->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $vendas->previousPageUrl() }}" rel="prev">&laquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($vendas->getUrlRange(1, $vendas->lastPage()) as $page => $url)
                    @if ($page == $vendas->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($vendas->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $vendas->nextPageUrl() }}" rel="next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">&raquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>
@endsection
