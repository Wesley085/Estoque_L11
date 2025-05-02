<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriasRequest;
use App\Models\Categorias;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categorias::all();
        return view('categorias.index',  ['categorias' => $categorias]);
    }

    public function create()
    {
        return view('categorias.addCategoria');
    }

    public function store(CategoriasRequest $request)
    {
        $categoria = new Categorias();
        $categoria->nome = $request->nome;
        $categoria->codigo = $request->codigo;

        if ($request->hasFile('icone') && $request->file('icone')->isValid()) {
            $requestIcon = $request->icone;
            $extension = $requestIcon->extension();
            $iconNome = md5($requestIcon->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestIcon->move(public_path('img/icon'), $iconNome);
            $categoria->icone = $iconNome;
        } else {
            $categoria->icone = null; // **Garante que o campo será salvo como NULL**
        }

        $categoria->descricao = $request->descricao;
        $categoria->save();

        return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso!');
    }


    public function show(string $id)
    {
        $categoria = Categorias::find($id);
        return view('produtos.index', compact('categorias'));
    }

    public function edit(string $id)
    {
        $categoria = Categorias::find($id);
        return view('categorias.editCategoria', compact('categoria'));
    }

    public function update(CategoriasRequest $request, string $id)
    {
        $categoria = Categorias::find($id);
        if (!$categoria) {
            return redirect()->route('produtos.index')->with('error', 'Produto não encontrado.');
        }
        $categoria->nome = $request->nome;
        $categoria->codigo = $request->codigo;
        if ($request->hasFile('icone') && $request->file('icone')->isValid()) {
            if ($categoria->icone && file_exists(public_path('img/icon/' . $categoria->icone))) {
                unlink(public_path('img/icon/' . $categoria->icone));
            }
            $requestIcon = $request->icone;
            $extension = $requestIcon->extension();
            $iconNome = md5($requestIcon->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestIcon->move(public_path('img/icon'), $iconNome);
            $categoria->icone = $iconNome;
        }
        $categoria->descricao = $request->descricao;
        $categoria->save();
        return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(string $id)
    {
        $categoria = Categorias::find($id);
        if ($categoria->produtos()->count() > 0) {
            return redirect()->back()->with('error', 'Não é possível excluir esta categoria, pois existem produtos vinculados a ela.');
        }
        if ($categoria) {
            if ($categoria->icone) {
                $imagePath = public_path('img/icon/' . $categoria->icone);

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $categoria->delete();

            return redirect()->route('categorias.index')->with('success', 'Categoria apagada com sucesso!');
        } else {
            return redirect()->route('categorias.index')->with('error', 'Categoria não encontrada.');
        }
    }
}
