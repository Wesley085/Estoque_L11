@extends('admin')

@section('content')

<div class="row mt-4 mb-4">
    <div class="col-12">
        <div class="card shadow">
            <!-- Filtros -->
            <div class="col-12">
                @include('partials.filtros_vendas', [
                    'titulo' => 'Vendas para ' . $entidade->nome, // Alterado aqui
                    'entidades' => collect([$entidade]),
                    'ocultarEntidade' => true,
                    'rotaFiltro' => route('entidades.vendas', $entidade->id),
                    'rotaLimpar' => route('entidades.vendas', $entidade->id),
                    'filtrosEspecificos' => '<input type="hidden" name="entidade_id" value="'.$entidade->id.'">'
                ])
            </div>

            <!-- Corpo da tabela -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="20%"><i class="far fa-calendar-alt mr-2"></i> Data</th>
                                <th width="40%"><i class="fas fa-boxes mr-2"></i> Produtos</th>
                                <th width="15%" class="text-end"><i class="fas fa-dollar-sign mr-2"></i> Valor Total</th>
                                <th width="25%" class="text-center"><i class="fas fa-cogs mr-2"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendas as $venda)
                            <tr>
                                <td class="align-middle">{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($venda->produtos as $produto)
                                        <li class="py-1">
                                            <i class="fas fa-cube mr-2 text-secondary"></i>
                                            {{ $produto->nome }}
                                            <span class="badge bg-info text-dark ml-2">
                                                {{ $produto->pivot->quantidade }} x R$ {{ number_format($produto->pivot->valor_venda, 2) }}
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="align-middle text-end fw-bold text-success">
                                    R$ {{ number_format($venda->valor_total * 100, 2, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    <div class="d-flex justify-content-center">
                                        <!-- Botão PDF -->
                                        <a href="{{ route('vendas.PdfVenda', $venda->id) }}"
                                           class="btn btn-sm btn-primary me-2"
                                           title="Gerar PDF desta venda">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        <!-- Botão Editar -->
                                        <a href="{{ route('vendas.edit', $venda->id) }}"
                                           class="btn btn-sm btn-warning me-2"
                                           title="Editar venda">
                                            <i class="fas fa-edit"></i>
                                        </a>

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
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginação -->
            @if($vendas->hasPages())
            <div class="card-footer bg-white">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
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
</div>
@endsection
