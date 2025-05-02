<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidade extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'cnpj', 'ativo'];

    public function vendas()
    {
        return $this->hasMany(Venda::class)->with('produtos');
    }
}

