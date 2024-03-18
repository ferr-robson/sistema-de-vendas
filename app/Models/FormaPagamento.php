<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormaPagamento extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function parcelas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }
}
