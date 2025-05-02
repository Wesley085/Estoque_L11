<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12 rounded shadow bg-white p-4 border">
            <form id="search-form" class="form-inline">
                <div class="row w-100">
                    <div class="col-md-6 col-12 mb-3">
                        <div class="form-group">
                            <label for="category" class="fw-bold">Categoria:</label>
                            <select wire:model.live="category" id="category" class="form-control w-100">
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-3">
                        <div class="form-group">
                            <label for="name" class="fw-bold">Produto:</label>
                            <input wire:model.live="name" type="text" id="name" class="form-control w-100" placeholder="Digite o nome do produto">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="text-end mt-3">
        <a href="{{ route('produtos.cadastrar') }}" class="btn btn-success shadow-sm">Adicionar novo Produto</a>
    </div>

    <div class="table-responsive mt-3">
        <table class="table shadow-sm table-bordered table-hover table-striped">
            <thead class="table-primary text-center">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Imagem</th>
                    <th>Valor</th>
                    <th>Categoria</th>
                    <th>Quantidade</th>
                    <th colspan="3">Opções</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produtos as $produto)
                <tr class="text-center align-middle">
                    <td>{{ $produto->id }}</td>
                    <td>{{ $produto->nome }}</td>
                    <td>
                        <img src="/img/events/{{ $produto->imagem }}" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
                    </td>
                    <td>R${{ $produto->valor_compra }}</td>
                    <td>{{ $produto->categoria->nome }}</td>
                    <td>{{ $produto->quantidade }}</td>
                    <td>
                        <a href="{{ route('produtos.show', $produto->id) }}"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#viewProductModal{{ $produto->id }}"
                            title="Visualizar">
                             <i class="far fa-eye"></i>
                         </a>
                    </td>
                    <td>
                        <a class="btn btn-warning btn-sm" href="{{ route('produtos.edit', $produto->id) }}" title="Editar produto">
                            <i class="far fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $produto->id }}">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @foreach ($produtos as $produto)
    <!-- Modal de Exclusão -->
    <div class="modal fade" id="confirmDeleteModal{{ $produto->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o produto <strong>{{ $produto->nome }}</strong>?</p>
                    <p class="text-muted">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="post" class="d-inline">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização -->
    <div class="modal fade" id="viewProductModal{{ $produto->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalhes do Produto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="/img/events/{{ $produto->imagem }}" alt="{{ $produto->nome }}"
                                 class="img-fluid rounded mb-3" style="max-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nome do Produto</label>
                                <p class="fs-5">{{ $produto->nome }}</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Valor</label>
                                    <p class="fs-5">R$ {{ number_format($produto->valor_compra, 2, ',', '.') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Categoria</label>
                                    <p class="fs-5">{{ $produto->categoria->nome }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Estoque</label>
                                    <p class="fs-5">
                                        <span class="badge {{ $produto->quantidade > 10 ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $produto->quantidade }} unidades
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Cadastrado em</label>
                                    <p class="fs-5">{{ $produto->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-primary">
                        <i class="far fa-edit me-1"></i> Editar Produto
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
</div>
