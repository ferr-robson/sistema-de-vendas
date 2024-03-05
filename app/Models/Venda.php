<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Parcela;
use App\Models\Produto;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'forma_pagamento_id', 'data_venda', 'total_venda','parcelado'];

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class);
    }

    public function itens(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'item_vendas')->withPivot('quantidade');
    }
}
