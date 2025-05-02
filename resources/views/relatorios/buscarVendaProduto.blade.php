@extends('admin')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-pdf"></i> Gerar Relatório PDF</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendas.PdfProduto') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="produto_id" class="form-label">Selecione o Produto:</label>
                            <select name="produto_id" id="produto_id" class="form-select">
                                @foreach($produtos as $produto)
                                    <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" class="btn btn-primary me-md-2">
                                <i class="fas fa-file-pdf me-1"></i> Gerar Relatório
                            </button>
                            <a href="{{ route('vendas.downloadPdf') }}" class="btn btn-secondary">
                                <i class="fas fa-file-alt me-1"></i> Relatório Completo
                            </a>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mb-2">
        <div class="col-12">
            <div class="card shadow">
                <div class="col-12">
                    @include('partials.filtros_vendas', [
                        'titulo' => 'Planejamentos',
                        'entidades' => \App\Models\Entidade::all(), // Ou qualquer outra forma de obter as entidades
                        'rotaFiltro' => route('vendas.buscarVendaProduto'),
                        'rotaLimpar' => route('vendas.buscarVendaProduto')
                    ])
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover ">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">ID</th>
                                    <th width="15%">Data/Horário</th>
                                    <th width="12%" class="text-end">Total Venda</th>
                                    <th width="12%" class="text-end">Total Compra</th>
                                    <th width="12%" class="text-end">Imposto</th>
                                    <th width="12%" class="text-end">Frete</th>
                                    <th width="12%" class="text-end">Lucro</th>
                                    <th colspan="3" width="17%" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendas as $venda)
                                    @php
                                        // Cálculos corretos para cada venda
                                        $totalCompra = $venda->produtos->sum(function($produto) {
                                            return $produto->pivot->valor_compra * $produto->pivot->quantidade;
                                        });
                                        $totalVenda = $venda->valor_total * 100;
                                        $totalImposto = $venda->imposto;
                                        $totalFrete = $venda->frete;
                                        $lucro = $totalVenda - ($totalCompra + $totalImposto + $totalFrete);
                                    @endphp
                                    <tr>
                                        <td>{{ $venda->id }}</td>
                                        <td>{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">R$ {{ number_format($totalVenda, 2, ',', '.') }}</td>
                                        <td class="text-end">R$ {{ number_format($totalCompra, 2, ',', '.') }}</td>
                                        <td class="text-end">R$ {{ number_format($totalImposto, 2, ',', '.') }}</td>
                                        <td class="text-end">R$ {{ number_format($totalFrete, 2, ',', '.') }}</td>
                                        <td class="text-end {{ $lucro >= 0 ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format($lucro, 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <!-- Botão PDF -->
                                            <a href="{{ route('vendas.PdfVenda', $venda->id) }}"
                                               class="btn btn-sm btn-primary me-2"
                                               title="Gerar PDF desta venda">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <!-- Botão Editar -->
                                            <a href="{{ route('vendas.edit', $venda->id) }}"
                                               class="btn btn-sm btn-warning me-2"
                                               title="Editar venda">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <!-- Botão Excluir -->
                                            <form action="{{ route('vendas.destroy', $venda->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Excluir venda"
                                                        onclick="return confirm('Tem certeza que deseja excluir esta venda?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">Nenhuma venda registrada</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if($vendas->hasPages())
        <div class="card-footer bg-white">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3 mb-4">
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
</div>
@endsection
