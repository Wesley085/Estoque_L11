<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Venda;
use App\Models\Produtos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use JsonException;

class VendaController extends Controller
{
    public function index()
    {
        $produtos = Produtos::all();
        $entidades = Entidade::where('ativo', true)->get();
        return view('vendas.index', compact('produtos', 'entidades'));
    }

    public function store(Request $request)
    {
        // Validação inicial dos campos básicos
        $request->validate([
            'produtos' => 'required|string', // Primeiro validamos como string
            'entidade_id' => 'nullable|exists:entidades,id', // Modificado para nullable
            'imposto' => 'required|numeric|min:0',
            'frete' => 'required|numeric|min:0'
        ]);

        // Tentativa de decodificar o JSON
        try {
            $produtos = json_decode($request->produtos, true, 512, JSON_THROW_ON_ERROR);

            // Validação dos produtos decodificados
            $validator = Validator::make(['produtos' => $produtos], [
                'produtos' => 'required|array',
                'produtos.*.id' => 'required|exists:produtos,id',
                'produtos.*.quantidade' => 'required|integer|min:1',
                'produtos.*.valor_compra' => 'required|numeric|min:0.01',
                'produtos.*.valor_venda' => 'required|numeric|min:0.01|gte:produtos.*.valor_compra',
                'produtos.*.lucro' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Dados dos produtos inválidos');
            }

        } catch (JsonException $e) {
            Log::error('JSON inválido recebido:', [
                'input' => $request->all(),
                'error' => $e->getMessage()
            ]);
            return back()
                ->with('error', 'Formato inválido dos produtos: ' . $e->getMessage())
                ->withInput();
        }


        DB::beginTransaction();

        try {
            // Criação da venda
            $venda = Venda::create([
                'valor_total' => 0,
                'quantidade_total' => 0,
                'imposto' => 0,
                'frete' => 0,
                'lucro_total' => 0,
                'entidade_id' => $request->entidade_id
            ]);

            $totais = [
                'valor' => 0,
                'quantidade' => 0,
                'imposto' => 0,
                'frete' => 0,
                'lucro' => 0
            ];

            // Primeiro passamos para calcular o valor total
            foreach ($produtos as $produtoRequest) {
                $totais['valor'] += ($produtoRequest['valor_venda'] * $produtoRequest['quantidade']);
            }

            // Agora processamos cada produto com os valores corretos
            foreach ($produtos as $produtoRequest) {
                $produto = Produtos::findOrFail($produtoRequest['id']);

                // Calcula a parcela proporcional
                $valorProduto = $produtoRequest['valor_venda'] * $produtoRequest['quantidade'];
                $percentualProduto = $totais['valor'] > 0 ? ($valorProduto / $totais['valor']) : 0;

                $imposto = $request->imposto * $percentualProduto;
                $frete = $request->frete * $percentualProduto;
                $lucro = $produtoRequest['lucro'];

                $venda->produtos()->attach($produto->id, [
                    'quantidade' => $produtoRequest['quantidade'],
                    'valor_compra' => $produtoRequest['valor_compra'],
                    'valor_venda' => $produtoRequest['valor_venda'],
                    'valor_total' => $valorProduto,
                    'imposto' => $imposto,
                    'frete' => $frete,
                    'lucro' => $lucro
                ]);

                // Atualização dos totais
                $totais['quantidade'] += $produtoRequest['quantidade'];
                $totais['imposto'] += $imposto;
                $totais['frete'] += $frete;
                $totais['lucro'] += $lucro;

                // Atualização do estoque
                $produto->decrement('quantidade', $produtoRequest['quantidade']);
            }

            // Atualização dos totais da venda
            $venda->update([
                'valor_total' => $totais['valor'],
                'quantidade_total' => $totais['quantidade'],
                'imposto' => $totais['imposto'],
                'frete' => $totais['frete'],
                'lucro_total' => $totais['lucro'],
            ]);

            DB::commit();

            // Log de sucesso
            Log::info('Venda registrada com sucesso', [
                'venda_id' => $venda->id,
                'entidade_id' => $venda->entidade_id,
                'total' => $totais['valor']
            ]);

            return redirect()
                ->route('vendas.index')
                ->with('success', "Venda registrada! Lucro: R$ " . number_format($totais['lucro'], 2, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao processar venda:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except('produtos')
            ]);

            return back()
                ->with('error', 'Erro ao registrar venda: ' . $e->getMessage())
                ->withInput();
        }
    }
    // Adicione estes métodos ao VendaController
    public function edit(Venda $venda)
    {
        $produtos = Produtos::all();
        $entidades = Entidade::where('ativo', true)->get();

        return view('vendas.edit', [
            'venda' => $venda,
            'produtos' => $produtos,
            'entidades' => $entidades,
            'produtosVenda' => $venda->produtos()->withPivot([
                'quantidade',
                'valor_compra',
                'valor_venda',
                'valor_total',
                'imposto',
                'frete',
                'lucro'
            ])->get()
        ]);
    }

    public function update(Request $request, Venda $venda)
    {
        $request->validate([
            'produtos' => 'required|string',
            'entidade_id' => 'nullable|exists:entidades,id',
            'imposto' => 'required|numeric|min:0',
            'frete' => 'required|numeric|min:0'
        ]);

        try {
            $produtos = json_decode($request->produtos, true, 512, JSON_THROW_ON_ERROR);

            $validator = Validator::make(['produtos' => $produtos], [
                'produtos' => 'required|array',
                'produtos.*.id' => 'required|exists:produtos,id',
                'produtos.*.quantidade' => 'required|integer|min:1',
                'produtos.*.valor_compra' => 'required|numeric|min:0.01',
                'produtos.*.valor_venda' => 'required|numeric|min:0.01|gte:produtos.*.valor_compra',
                'produtos.*.lucro' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Dados dos produtos inválidos');
            }

            DB::beginTransaction();

            // Primeiro, devolvemos os produtos ao estoque
            foreach ($venda->produtos as $produto) {
                $produto->increment('quantidade', $produto->pivot->quantidade);
            }

            // Limpa os produtos atuais
            $venda->produtos()->detach();

            // Processa como no store
            $totais = [
                'valor' => 0,
                'quantidade' => 0,
                'imposto' => 0,
                'frete' => 0,
                'lucro' => 0
            ];

            foreach ($produtos as $produtoRequest) {
                $totais['valor'] += ($produtoRequest['valor_venda'] * $produtoRequest['quantidade']);
            }

            foreach ($produtos as $produtoRequest) {
                $produto = Produtos::findOrFail($produtoRequest['id']);

                $valorProduto = $produtoRequest['valor_venda'] * $produtoRequest['quantidade'];
                $percentualProduto = $totais['valor'] > 0 ? ($valorProduto / $totais['valor']) : 0;

                $imposto = $request->imposto * $percentualProduto;
                $frete = $request->frete * $percentualProduto;
                $lucro = $produtoRequest['lucro'];

                $venda->produtos()->attach($produto->id, [
                    'quantidade' => $produtoRequest['quantidade'],
                    'valor_compra' => $produtoRequest['valor_compra'],
                    'valor_venda' => $produtoRequest['valor_venda'],
                    'valor_total' => $valorProduto,
                    'imposto' => $imposto,
                    'frete' => $frete,
                    'lucro' => $lucro
                ]);

                $totais['quantidade'] += $produtoRequest['quantidade'];
                $totais['imposto'] += $imposto;
                $totais['frete'] += $frete;
                $totais['lucro'] += $lucro;

                $produto->decrement('quantidade', $produtoRequest['quantidade']);
            }

            $venda->update([
                'valor_total' => $totais['valor'],
                'quantidade_total' => $totais['quantidade'],
                'imposto' => $totais['imposto'],
                'frete' => $totais['frete'],
                'lucro_total' => $totais['lucro'],
                'entidade_id' => $request->entidade_id
            ]);

            DB::commit();
            return redirect()
                ->route('vendas.index') // Agora redireciona para a lista geral de vendas
                ->with('success', "Venda atualizada com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar venda:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }
    public function destroy(Venda $venda)
    {
        DB::beginTransaction();

        try {
            // Reverte as quantidades no estoque
            foreach ($venda->produtos as $produto) {
                $produto->increment('quantidade', $produto->pivot->quantidade);
            }

            // Remove a venda
            $venda->produtos()->detach();
            $venda->delete();

            DB::commit();
            return back()->with('success', "Venda excluída com sucesso!");
            // return redirect()
            //     ->route('vendas.index') // Agora redireciona para a lista geral de vendas
            //     ->with('success', "Venda excluída com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir venda:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Erro ao excluir venda: ' . $e->getMessage());
        }
    }
}
