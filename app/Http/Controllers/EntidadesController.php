<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntidadesRequest;
use App\Models\Entidade;
use Illuminate\Http\Request;

class EntidadesController extends Controller
{
    public function index()
    {
        $entidades = Entidade::all();
        return view('entidades.index', compact('entidades'));
    }

    public function create()
    {
        return view('entidades.addEntidade');
    }

    public function store(EntidadesRequest $request)
    {
        $data = $request->validated();
        $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']); // Remove formatação antes de salvar

        Entidade::create($data);
        return redirect()->route('entidades.index')->with('success', 'Entidade criada com sucesso!');
    }

    public function edit(Entidade $entidade)
    {
        return view('entidades.editEntidade', compact('entidade'));
    }

    public function update(EntidadesRequest $request, Entidade $entidade)
    {
        $data = $request->validated();
        $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);

        $entidade->update($data);
        return redirect()->route('entidades.index')->with('success', 'Entidade atualizada com sucesso!');
    }

    public function destroy(Entidade $entidade)
    {
        if ($entidade->vendas()->count() > 0) {
            return back()->with('error', 'Esta entidade possui vendas vinculadas!');
        }
        $entidade->delete();
        return redirect()->route('entidades.index')->with('success', 'Entidade removida com sucesso!');
    }
}

