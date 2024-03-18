<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\User;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = ['vendedor_id', 'cliente_id', 'forma_pagamento_id', 'data_venda', 'total_venda','parcelado'];

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class);
    }

    public function itens(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'item_vendas')->withPivot('quantidade', 'preco');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forma_pagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }
}
