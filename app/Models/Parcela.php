<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Venda;

class Parcela extends Model
{
    use HasFactory;
    
    protected $fillable = ['venda_id', 'data_vencimento', 'valor_parcela'];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }
}
