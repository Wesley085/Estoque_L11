@extends('admin')

@section('scripts')
    <script src="{{ asset('js/scanner.js') }}"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection

@section('content')
    @include('mensagens.mensagem')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-12 rounded shadow bg-white p-4 border">
            <form id="search-form" class="form-inline">
                <div class="row w-100">
                    <div class="col-md-6 col-12 mb-3">
                        <label for="nome" class="fw-bold">Nome:</label>
                        <input wire:model.live="nome" type="text" id="nome" class="form-control w-100" placeholder="Digite o nome da entidade">
                    </div>
                    <div class="col-md-6 col-12 mb-3">
                        <label for="cnpj" class="fw-bold">CNPJ:</label>
                        <input wire:model.live="cnpj" type="text" id="cnpj" class="form-control w-100" placeholder="Digite o CNPJ">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="text-end mt-3">
            <a href="{{ route('entidades.create') }}" class="btn btn-success">Adicionar nova Entidade</a>
        </div>
    <div class="table-responsive mt-3">
        <table class="table shadow-sm table-bordered table-hover table-striped">
            <thead class="text-center table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Vendas</th>
                    <th colspan="3">Opções</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entidades as $entidade)
                    <tr>
                        <td class="text-center align-middle">{{ $entidade->id }}</td>
                        <td class="text-center align-middle">{{ $entidade->nome }}</td>
                        <td class="text-center align-middle">{{ $entidade->cnpj }}</td>
                        <td class="text-center align-middle">
                            <a href="{{ route('entidades.vendas', $entidade->id) }}"
                                class="btn btn-primary btn-sm" data-toggle="tooltip" title="Ver vendas">
                                {{ $entidade->vendas->count() }} vendas
                            </a>
                        </td>
                        <td class="text-center align-middle pt-3 pb-3">
                            <button type="button"
                                    class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewEntidadeModal{{ $entidade->id }}"
                                    title="Visualizar">
                                <i class="far fa-eye"></i>
                            </button>
                        </td>

                        <td class="text-center align-middle pt-3 pb-3">
                            <div class="btn-group" role="group">
                                <!-- Editar -->
                                <a href="{{ route('entidades.edit', $entidade->id) }}"
                                   class="btn btn-warning btn-sm"
                                   title="Editar">
                                    <i class="far fa-edit"></i>
                                </a>
                            </td>
                            <td class="text-center align-middle">
                                <!-- Excluir -->
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModalEntidade{{ $entidade->id }}"
                                        title="Excluir">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal de confirmação --}}
                    <div class="modal fade" id="confirmDeleteModalEntidade{{ $entidade->id }}" tabindex="-1"
                        aria-labelledby="confirmDeleteModalEntidade" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    Tem certeza que deseja excluir esta entidade?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('entidades.destroy', $entidade->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal de Visualização da Entidade -->
                    <div class="modal fade" id="viewEntidadeModal{{ $entidade->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Detalhes da Entidade</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Nome da Entidade</label>
                                                <p class="fs-5">{{ $entidade->nome }}</p>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted">CNPJ</label>
                                                    <p class="fs-5">{{ $entidade->cnpj }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted">Vendas Associadas</label>
                                                    <p class="fs-5">{{ $entidade->vendas->count() }}</p>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label text-muted">Cadastrada em</label>
                                                <p class="fs-5">{{ $entidade->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4 text-center">
                                            <!-- Espaço para possível imagem/logo da entidade -->
                                            <div class="bg-light rounded p-4 d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-building fa-5x text-muted"></i>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    <a href="{{ route('entidades.edit', $entidade->id) }}" class="btn btn-primary">
                                        <i class="far fa-edit me-1"></i> Editar Entidade
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

