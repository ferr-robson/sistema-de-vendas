<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Venda;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'preco'];

    public function vendas(): BelongsToMany
    {
        return $this->belongsToMany(Venda::class, 'item_vendas');
    }
}
