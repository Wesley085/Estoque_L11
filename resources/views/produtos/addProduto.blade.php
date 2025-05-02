@extends('admin')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row justify-content-md-center bg-light ">
    <div class="col-sm-10 rounded bg-white p-3 m-1 border mt-5 p-md-5">
        <h2>Cadastrar Produtos</h2>
        <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mt-3">
                <label for="nome">Nome do Produto</label>
                <input type="text" class="form-control mt-2" required name="nome" id="nome">
            </div>

            <div class="mt-3">
                <label for="inputGroupFile02" class="form-label">Imagem do Produto</label>
                <input type="file" class="form-control" id="imagem" name="imagem">
            </div>

            <!-- Campo Valor de Compra (antigo "valor") -->
            <div class="form-group mt-3">
                <label for="valor_compra">Valor de Compra</label>
                <input type="number" class="form-control mt-2" required name="valor_compra" id="valor_compra" step="0.01" min="0">
            </div>

            {{-- <!-- Novo Campo Valor de Venda -->
            <div class="form-group mt-3">
                <label for="valor_venda">Valor de Venda</label>
                <input type="number" class="form-control mt-2" required name="valor_venda" id="valor_venda" step="0.01" min="0">
                <small class="text-muted">Deve ser maior ou igual ao valor de compra</small>
            </div> --}}

            <div class="form-group mt-3">
                <label for="categoria_id">Categoria do Produto</label>
                    <select name="categoria_id" id="categoria_id" class="form-control mt-2">
                        <option value="">Selecione uma categoria</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="form-group mt-3">
                <label for="quantidade">Quantidades em estoque</label>
                <input type="number" class="form-control mt-2" name="quantidade" id="quantidade" min="0">
            </div>
            <button class="btn btn-primary mt-4"><i class="fa fa-save"></i> Cadastrar produto</button>
        </form>
    </div>
</div>

<!-- Adicione este script para validação client-side -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const valorCompra = document.getElementById('valor_compra');
    const valorVenda = document.getElementById('valor_venda');

    valorVenda.addEventListener('change', function() {
        if (parseFloat(valorVenda.value) < parseFloat(valorCompra.value)) {
            alert('O valor de venda não pode ser menor que o valor de compra!');
            valorVenda.value = valorCompra.value;
        }
    });
});
</script>
@endsection
