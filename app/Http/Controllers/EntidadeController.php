<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EntidadeController extends Controller
{
// app/Http/Controllers/EntidadeController.php
    public function vendas(Entidade $entidade, Request $request)
    {
        $query = $entidade->vendas()
                    ->with(['produtos' => function($query) {
                        $query->withPivot([
                            'quantidade',
                            'valor_venda',
                            'valor_total'
                        ]);
                    }])
                    ->latest();

        // Aplica os mesmos filtros
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

        if ($request->filled('valor_min')) {
            $query->where('valor_total', '>=', $request->valor_min);
        }
        if ($request->filled('valor_max')) {
            $query->where('valor_total', '<=', $request->valor_max);
        }

        $vendas = $query->paginate(10);

        return view('entidades.vendas', [
            'entidade' => $entidade,
            'vendas' => $vendas,
            'totalVendas' => $vendas->sum('valor_total')
        ]);
    }
}
