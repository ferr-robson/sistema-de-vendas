<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'forma_pagamento_id' => 'exists:forma_pagamentos,id',
            'vendedor_id' => 'exists:users,id',
            'total_venda' => 'numeric|min:0',
            'parcelado' => 'boolean',
            'produtos' => 'array', 
            'produtos.*.produto_id' => 'exists:produtos,id', 
            'produtos.*.quantidade' => 'numeric',
            'qtde_parcelas' => 'nullable|numeric|min:0',
        ];
    }
}
