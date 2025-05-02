<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProdutoVenda extends Pivot
{
    protected $table = 'produto_venda';

    protected $fillable = [
        'venda_id',
        'produtos_id', // Mantido para consistência com sua estrutura atual
        'quantidade',
        'valor_compra',
        'valor_venda',
        'valor_total',
        'imposto',
        'frete',
        'lucro'
    ];

    // Adicione os casts para garantir o formato decimal
    protected $casts = [
        'valor_compra' => 'float',
        'valor_venda' => 'float',
        'valor_total' => 'float',
        'imposto' => 'float',
        'frete' => 'float',
        'lucro' => 'float'
    ];

    // Correção no relacionamento (note o 'produtos_id' ao invés de 'produto_id')
    public function produto()
    {
        return $this->belongsTo(Produtos::class, 'produtos_id'); // Corrigido para match com sua migration
    }
    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }
}
