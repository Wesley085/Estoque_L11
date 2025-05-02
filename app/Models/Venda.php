<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor_total',
        'quantidade_total',
        'imposto',
        'frete',
        'lucro_total',
        'created_at',
        'updated_at',
        'entidade_id'
    ];

    public function produtos()
    {
        return $this->belongsToMany(Produtos::class, 'produto_venda')
                    ->using(ProdutoVenda::class)
                    ->withPivot([
                        'quantidade',
                        'valor_compra',
                        'valor_venda',
                        'valor_total',
                        'imposto',
                        'frete',
                        'lucro'
                        // Não inclua entidade_id aqui
                    ])
                    ->withTimestamps();
    }

    public function produtoVendas()
    {
        return $this->hasMany(ProdutoVenda::class, 'venda_id');
    }
    protected $casts = [
        'valor_total' => 'float',
        'imposto' => 'float',
        'frete' => 'float',
        'lucro_total' => 'float'
    ];
    // Para cada campo monetário
    public function setValorTotalAttribute($value)
    {
        $this->attributes['valor_total'] = $value;
    }

    public function getValorTotalAttribute($value)
    {
        return $value / 100;
    }
    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }
}
