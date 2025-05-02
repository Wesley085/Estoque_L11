<div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
    <h4 class="mb-2"><i class="fas fa-list"></i> {{ $titulo ?? 'Vendas' }}</h4>
    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
        <i class="fas fa-filter"></i> Filtros
    </button>
</div>

<div class="collapse" id="filtrosCollapse">
    <div class="card-body bg-light">
        <form method="GET" action="{{ $rotaFiltro ?? route('vendas.listar') }}">
            @if(isset($filtrosEspecificos))
                {!! $filtrosEspecificos !!}
            @endif

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

                @if(!isset($ocultarEntidade))
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
                @endif

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
                    <a href="{{ $rotaLimpar ?? $rotaFiltro ?? route('vendas.listar') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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
    if (form) {
        form.addEventListener('submit', function(e) {
            const dataInicio = document.getElementById('data_inicio').value;
            const dataFim = document.getElementById('data_fim').value;

            if (dataInicio && dataFim && dataInicio > dataFim) {
                alert('A data de início não pode ser maior que a data final!');
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush


