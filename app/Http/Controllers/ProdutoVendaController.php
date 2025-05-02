<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\ProdutoVenda;
use App\Models\Venda;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProdutoVendaController extends Controller
{
    public function downloadPdf()
    {
        $vendas = ProdutoVenda::with(['produto', 'venda'])
                    ->whereHas('produto')
                    ->whereHas('venda')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Cálculos dos totais
        $totais = [
            'faturado' => $vendas->sum('valor_total'),
            'frete' => $vendas->sum('frete'),
            'imposto' => $vendas->sum('imposto'),
            'compra' => $vendas->sum(function($v) {
                return $v->valor_compra * $v->quantidade;
            }),
        ];

        $totais['liquido'] = $totais['faturado'] - ($totais['imposto'] + $totais['frete']);
        $totais['lucro'] = $totais['faturado'] - ($totais['compra'] + $totais['imposto'] + $totais['frete']);

        $pdf = Pdf::loadView('relatorios.vendas', [
            'vendas' => $vendas,
            'totais' => $totais,
            'periodo' => now()->subDays(30)->format('d/m/Y') . ' a ' . now()->format('d/m/Y')
        ]);

        return $pdf->download('relatorio-geral-vendas.pdf');
    }

    // public function buscarVendaProduto()
    // {
    //     return view('relatorios.buscarVendaProduto', [
    //         'produtos' => Produtos::all(),
    //         'vendas' => Venda::latest()->paginate(10)
    //     ]);
    // }

    public function PdfProduto(Request $request)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
        ]);

        $produto = Produtos::findOrFail($request->produto_id);
        $produtoVendas = ProdutoVenda::where('produtos_id', $request->produto_id) // Note o 'produtos_id' aqui
                        ->with('produto')
                        ->get();

        $pdf = Pdf::loadView('relatorios.vendaProduto', [
            'produtoVendas' => $produtoVendas,
            'produto' => $produto,
            'faturado' => $produtoVendas->sum('valor_total')
        ]);

        return $pdf->download("relatorio-produto-{$produto->id}.pdf");
    }

    public function PdfVenda($venda_id)
    {
        $venda = Venda::with(['produtos' => function($query) {
            $query->withPivot([
                'quantidade',
                'valor_compra',
                'valor_venda',
                'imposto',
                'frete',
                'lucro'
            ]);
        }])->findOrFail($venda_id);

        // Cálculos dos totais
        $totais = [
            'compra' => $venda->produtos->sum(function($produto) {
                return $produto->pivot->valor_compra * $produto->pivot->quantidade;
            }),
            'venda' => $venda->produtos->sum(function($produto) {
                return $produto->pivot->valor_venda * $produto->pivot->quantidade;
            }),
            'imposto' => $venda->imposto, // Pega direto da venda
            'frete' => $venda->frete, // Pega direto da venda
        ];

        $totais['lucro'] = $totais['venda'] - ($totais['compra'] + $totais['imposto'] + $totais['frete']);

        $pdf = Pdf::loadView('relatorios.venda', [
            'venda' => $venda,
            'totais' => $totais,
            'data' => $venda->created_at->format('d/m/Y H:i')
        ]);

        return $pdf->download("relatorio-venda-{$venda->id}.pdf");
    }

    public function buscarVendaProduto(Request $request)
    {
        $query = Venda::with(['produtos' => function($query) {
            $query->withPivot([
                'quantidade',
                'valor_compra',
                'valor_venda',
                'imposto',
                'frete'
            ]);
        }])->latest();

        // Aplica os filtros
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('created_at', [
                $request->data_inicio,
                Carbon::parse($request->data_fim)->endOfDay()
            ]);
        } elseif ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        } elseif ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        if ($request->filled('entidade_id')) {
            $query->where('entidade_id', $request->entidade_id);
        }

        if ($request->filled('valor_min')) {
            $query->where('valor_total', '>=', $request->valor_min);
        }

        if ($request->filled('valor_max')) {
            $query->where('valor_total', '<=', $request->valor_max);
        }

        $vendas = $query->paginate(10);

        return view('relatorios.buscarVendaProduto', [
            'produtos' => Produtos::all(),
            'vendas' => $vendas
        ]);
    }
}
