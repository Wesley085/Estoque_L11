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
        <h2>Editar Entidade</h2>  <!-- Título alterado -->
        <form action="{{ route('entidades.update', $entidade->id) }}" method="POST">
            @csrf
            @method('PUT')  <!-- Adicionado o método PUT para atualização -->

            <div class="form-group mt-3">
                <label for="nome">Nome da Entidade</label>
                <input type="text" class="form-control mt-2" required name="nome" id="nome"
                       value="{{ old('nome', $entidade->nome) }}">  <!-- Preenche com o valor existente -->
            </div>

            <div class="form-group mt-3">
                <label for="cnpj">CNPJ</label>
                <input type="text" class="form-control mt-2" required name="cnpj" id="cnpj"
                       placeholder="00.000.000/0000-00" value="{{ old('cnpj', $entidade->cnpj) }}">  <!-- Preenche CNPJ -->
            </div>

            <button class="btn btn-primary mt-4"><i class="fa fa-save"></i> Atualizar Entidade</button>  <!-- Texto do botão alterado -->
        </form>
    </div>
</div>

<script>
    document.getElementById('cnpj').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) value = value.substring(0, 2) + '.' + value.substring(2);
        if (value.length > 6) value = value.substring(0, 6) + '.' + value.substring(6);
        if (value.length > 10) value = value.substring(0, 10) + '/' + value.substring(10);
        if (value.length > 15) value = value.substring(0, 15) + '-' + value.substring(15);
        e.target.value = value.substring(0, 18);
    });
</script>
@endsection
