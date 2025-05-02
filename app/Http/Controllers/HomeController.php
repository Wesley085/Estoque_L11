<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Produtos;
use App\Models\ProdutoVenda;
use App\Models\Venda;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $periodo = now()->subDays(30);

        // Obter os dados das vendas
        $vendasQuery = ProdutoVenda::with(['produto', 'venda'])
            ->whereBetween('created_at', [$periodo, now()])
            ->orderByDesc('created_at');

        // Primeiro obtemos os dados paginados
        $vendasPaginadas = $vendasQuery->paginate(10);

        // Depois calculamos os totais com uma nova consulta
        $totais = [
            'faturado' => ProdutoVenda::whereBetween('created_at', [$periodo, now()])->sum('valor_total'),
            'frete' => ProdutoVenda::whereBetween('created_at', [$periodo, now()])->sum('frete'),
            'imposto' => ProdutoVenda::whereBetween('created_at', [$periodo, now()])->sum('imposto'),
            'compra' => $this->calculateTotalCompra($periodo),
        ];

        $totais['lucro'] = $totais['faturado'] - ($totais['compra'] + $totais['imposto'] + $totais['frete']);

        return view('home', [
            'vendas' => $vendasPaginadas,
            'totais' => $totais,
            'totalProdutosVendidos' => ProdutoVenda::whereBetween('created_at', [$periodo, now()])->sum('quantidade'),
            'vendasTotais' => Venda::whereBetween('created_at', [$periodo, now()])->count(),
            'estoqueBaixo' => Produtos::where('quantidade', '<', 10)->count(),
            'periodo' => $periodo->format('d/m/Y') . ' - ' . now()->format('d/m/Y')
        ]);
    }

    protected function calculateTotalCompra(Carbon $periodo): float
    {
        $total = 0;
        $produtosVenda = ProdutoVenda::whereBetween('created_at', [$periodo, now()])->get();

        foreach ($produtosVenda as $venda) {
            $total += $venda->valor_compra * $venda->quantidade;
        }

        return $total;
    }

    // Manter os outros métodos conforme existiam anteriormente
    protected function ultimasVendas(Carbon $periodo)
    {
        return ProdutoVenda::with('produto')
            ->whereBetween('created_at', [$periodo, now()])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends(request()->query());
    }

    protected function totalProdutosVendidos(Carbon $periodo): int
    {
        return ProdutoVenda::whereBetween('created_at', [$periodo, now()])
            ->sum('quantidade');
    }

    protected function valorFaturadoFormatado(Carbon $periodo): string
    {
        $total = Venda::whereBetween('created_at', [$periodo, now()])
            ->sum('valor_total'); // Alterado para valor_total para consistência

        return number_format($total, 2, ',', '.');
    }

    protected function totalVendas(Carbon $periodo): int
    {
        return Venda::whereBetween('created_at', [$periodo, now()])
            ->count();
    }

    protected function produtosComEstoqueBaixo(): int
    {
        return Produtos::where('quantidade', '<', 10)
            ->count();
    }
}
