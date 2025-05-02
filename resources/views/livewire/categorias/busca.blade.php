<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12 rounded shadow bg-white p-4 border">
            <form id="search-form" class="form-inline">
                <div class="row w-100">
                    <div class="col-md-6 col-12 mb-3">
                        <div class="form-group">
                            <label for="code" class="fw-bold">Código:</label>
                            <input wire:model.live="code" type="text" id="code" class="form-control w-100" placeholder="Digite o código da categoria">
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-3">
                        <div class="form-group">
                            <label for="nome" class="fw-bold">Nome:</label>
                            <input wire:model.live="nome" type="text" id="nome" class="form-control w-100" placeholder="Digite o nome da categoria">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="text-end mt-3">
        <a href="{{ route('categorias.cadastrar') }}" class="btn btn-success shadow-sm">Adicionar nova Categoria</a>
    </div>

    <div class="table-responsive mt-3">
        <table class="table shadow-sm table-bordered table-hover table-striped">
            <thead class="table-primary text-center">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Ícone</th>
                    <th>Descrição</th>
                    <th colspan="3">Opções</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categorias as $categoria)
                <tr class="text-center align-middle">
                    <td>{{ $categoria->id }}</td>
                    <td>{{ $categoria->nome }}</td>
                    <td>{{ $categoria->codigo }}</td>
                    <td>
                        <img src="/img/icon/{{ $categoria->icone }}" alt=" " class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;" >
                    </td>
                    <td>{{ $categoria->descricao }}</td>
                    <td>
                        <a href="{{ route('categorias.show', $categoria->id) }}"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#viewCategoryModal{{ $categoria->id }}"
                            title="Visualizar">
                             <i class="far fa-eye"></i>
                         </a>
                    </td>
                    <td>
                        <a class="btn btn-warning btn-sm" href="{{ route('categorias.edit', $categoria->id) }}" title="Editar categoria">
                            <i class="far fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $categoria->id }}">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach ($categorias as $categoria)
    <!-- Modal de Exclusão -->
    <div class="modal fade" id="confirmDeleteModal{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a categoria <strong>{{ $categoria->nome }}</strong>?</p>
                    <p class="text-muted">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('categorias.destroy', $categoria->id) }}" method="post" class="d-inline">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização -->
    <div class="modal fade" id="viewCategoryModal{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalhes da Categoria</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="/img/icon/{{ $categoria->icone }}" alt=""
                                 class="img-fluid rounded mb-3" style="max-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nome da Categoria</label>
                                <p class="fs-5">{{ $categoria->nome }}</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Código</label>
                                    <p class="fs-5">{{ $categoria->codigo }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Produtos associados</label>
                                    <p class="fs-5">{{ $categoria->produtos->count() }}</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Descrição</label>
                                <p class="fs-5">{{ $categoria->descricao }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Cadastrada em</label>
                                <p class="fs-5">{{ $categoria->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-primary">
                        <i class="far fa-edit me-1"></i> Editar Categoria
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <style>
        /* Estilos personalizados */
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .form-floating label {
            color: #6c757d;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .btn-group .btn {
                flex: 1;
                min-width: 60px;
            }
        }
    </style>
</div>
