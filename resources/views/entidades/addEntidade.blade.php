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
        <h2>Cadastrar Entidade</h2>
        <form action="{{ route('entidades.store') }}" method="POST">
            @csrf
            <div class="form-group mt-3">
                <label for="nome">Nome da Entidade</label>
                <input type="text" class="form-control mt-2" required name="nome" id="nome">
            </div>
            <div class="form-group mt-3">
                <label for="cnpj">CNPJ</label>
                <input type="text" class="form-control mt-2" required name="cnpj" id="cnpj" placeholder="00.000.000/0000-00">
            </div>
            <button class="btn btn-primary mt-4"><i class="fa fa-save"></i> Cadastrar Entidade</button>
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
