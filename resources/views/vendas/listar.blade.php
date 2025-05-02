@extends('admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-list"></i> Todas as Vendas</h4>
        </div>
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="fas fa-list"></i> Planejamentos</h4>
    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
        <i class="fas fa-filter"></i> Filtros
    </button>
</div>

<div class="collapse" id="filtrosCollapse">
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('vendas.listar') }}">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="id" class="form-label">ID</label>
                    <input type="number" class="form-control" id="id" name="id" value="{{ request('id') }}">
                </div>

                <div class="col-md-2">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
                </div>

                <div class="col-md-2">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
                </div>

                <div class="col-md-3">
                    <label for="entidade_id" class="form-label">Entidade</label>
                    <select class="form-select" id="entidade_id" name="entidade_id">
                        <option value="">Todas</option>
                        @foreach($entidades as $entidade)
                            <option value="{{ $entidade->id }}" {{ request('entidade_id') == $entidade->id ? 'selected' : '' }}>
                                {{ $entidade->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="valor_min" class="form-label">Valor Mínimo</label>
                    <input type="number" step="0.01" class="form-control" id="valor_min" name="valor_min" value="{{ request('valor_min') }}">
                </div>

                <div class="col-md-2">
                    <label for="valor_max" class="form-label">Valor Máximo</label>
                    <input type="number" step="0.01" class="form-control" id="valor_max" name="valor_max" value="{{ request('valor_max') }}">
                </div>

                <div class="col-md-12 d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('vendas.listar') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Entidade</th>
                            <th>Total</th>
                            <th>Lucro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendas as $venda)
                        <tr>
                            <td>{{ $venda->id }}</td>
                            <td>{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $venda->entidade->nome ?? 'Nenhuma' }}</td>
                            <td>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                            <td class="{{ $venda->lucro_total >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($venda->lucro_total, 2, ',', '.') }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('vendas.edit', $venda->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('vendas.destroy', $venda->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tem certeza?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('vendas.PdfVenda', $venda->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $vendas->links() }}
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mantém os filtros visíveis se algum estiver preenchido
        const hasFilters = window.location.search.includes('?') &&
                          !window.location.search.includes('page=');

        if (hasFilters) {
            const collapseElement = document.getElementById('filtrosCollapse');
            const bsCollapse = new bootstrap.Collapse(collapseElement, {
                toggle: true
            });
        }

        // Validação das datas
        const form = document.querySelector('#filtrosCollapse form');
        form.addEventListener('submit', function(e) {
            const dataInicio = document.getElementById('data_inicio').value;
            const dataFim = document.getElementById('data_fim').value;

            if (dataInicio && dataFim && dataInicio > dataFim) {
                alert('A data de início não pode ser maior que a data final!');
                e.preventDefault();
            }
        });
    });
    </script>
@endsection
