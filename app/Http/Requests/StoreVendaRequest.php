<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendaRequest extends FormRequest
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
            'forma_pagamento_id' => 'required|exists:forma_pagamentos,id',
            'parcelado' => 'boolean',
            'produtos' => 'required|array', 
            'produtos.*.produto_id' => 'required|exists:produtos,id', 
            'produtos.*.quantidade' => 'required|numeric',
            'qtde_parcelas' => 'nullable|numeric|min:0',
        ];
    }
}
