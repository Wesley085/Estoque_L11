<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutosRequest;
use App\Models\Categorias;
use App\Models\Produtos;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ProdutosController extends Controller
{
    public function index()
    {
        $produtos = Produtos::all();
        return view('produtos.index',  ['produtos' => $produtos]);
    }

    public function create()
    {
        $categorias = Categorias::all();

        return view('produtos.addProduto', compact('categorias'));
    }

    public function store(ProdutosRequest $request)
    {
        $produtos = new Produtos();
        $produtos->nome = $request->nome;
        if($request->hasFile('imagem') && $request->file('imagem')->isValid()){
            $requestImagem = $request->imagem;
            $extension = $requestImagem->extension();
            $imagemNome = md5($requestImagem->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImagem->move(public_path('img/events'), $imagemNome);
            $produtos->imagem = $imagemNome;
        }
        // $produtos->valor = $request->valor;
        $produtos->valor_compra = $request->valor_compra;
        $produtos->categoria_id = $request->categoria_id;
        $produtos->quantidade = $request->quantidade;
        $produtos->save();
        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }

    public function show(string $id)
    {
        $produto = Produtos::find($id);
        $categorias = Categorias::all();
        return view('produtos.index', compact('produto','categorias'));
    }
    

    public function edit(string $id)
    {
        $produto = Produtos::find($id);
        $categorias = Categorias::all();

        if (!$produto) {
            return redirect()->route('produtos.index')->with('error', 'Produto não encontrado.');
        }

        return view('produtos.editProduto', compact('produto','categorias'));
    }

    public function update(ProdutosRequest $request, string $id)
    {
        $produtos = Produtos::find($id);
        if (!$produtos) {
            return redirect()->route('produtos.index')->with('error', 'Produto não encontrado.');
        }
        $produtos->nome = $request->nome;
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            if ($produtos->imagem && file_exists(public_path('img/events/' . $produtos->imagem))) {
                unlink(public_path('img/events/' . $produtos->imagem));
            }
            $requestImagem = $request->imagem;
            $extension = $requestImagem->extension();
            $imagemNome = md5($requestImagem->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImagem->move(public_path('img/events'), $imagemNome);
            $produtos->imagem = $imagemNome;
        }
        // $produtos->valor = $request->valor;
        $produtos->valor_compra = $request->valor_compra; // Alterado de 'valor'
        $produtos->categoria_id = !empty($request->categoria_id) ? $request->categoria_id : null;
        $produtos->quantidade = $request->quantidade;
        $produtos->save();
        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        $produto = Produtos::find($id);

        if ($produto) {
            if ($produto->imagem) {
                $imagePath = public_path('img/events/' . $produto->imagem);

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $produto->delete();

            return redirect()->route('produtos.index')->with('success', 'Produto apagado com sucesso!');
        } else {
            return redirect()->route('produtos.index')->with('error', 'Produto não encontrado.');
        }
    }
    public function getProduto($id)
    {
        $produto = Produtos::find($id);
        if ($produto) {
            return response()->json([
                'valor_compra' =>  $produto->valor_compra, // Alterado de 'valor'
            ]);
        }
        return response()->json(['error' => 'Produto não encontrado'], 404);
    }
    public function downloadPdfProduto()
    {
        try {
            $produtos = Produtos::all();

            if ($produtos->isEmpty()) {
                return response()->json(['error' => 'Nenhum produto encontrado'], 404);
            }

            $pdf = PDF::loadView('relatorios.produtos', compact('produtos'));

            return $pdf->download('relatorio-todos-produtos.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }
}
