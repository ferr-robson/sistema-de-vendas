<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Parcela;
use App\Models\ItemVenda;
use App\Models\Produto;
// use App\Http\Requests\StoreVendaRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateVendaRequest;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // tabela de vendas
            $request->validate([
                'cliente' => 'nullable|exists:clientes,id',
                'forma_pagamento' => 'required|exists:forma_pagamentos,id',
                'total_venda' => 'required|numeric|min:0',
                'parcelado' => 'boolean',
                
                'produtos' => 'required|array', 
                'produtos.*' => 'required|array', 
                'produtos.*.produto_id' => 'required|exists:produtos,id', 
                'produtos.*.quantidade' => 'required|numeric',
                
                'qtde_parcelas' => 'nullable|numeric|min:0',
            ]);
    
            $data_atual = new \DateTime();
            
            $venda = new Venda();
            $venda->cliente_id = $request->cliente;
            $venda->forma_pagamento_id = $request->forma_pagamento;
            $venda->data_venda = $data_atual;
            $venda->total_venda = $request->total_venda;
            $venda->parcelado = $request->parcelado;
    
            $venda->save();
    
            // return response()->json($venda, 201);
            // /tabela de vendas

            // tabela de produto vendas
            foreach ($request->produtos as $produto) {
                $item = Produto::findOrFail($produto['produto_id']);

                $itemVenda = new ItemVenda();
                $itemVenda->venda_id = $venda->id;
                $itemVenda->produto_id = $produto['produto_id'];
                $itemVenda->quantidade = $produto['quantidade'];
                $itemVenda->preco = $item->preco / $produto['quantidade'];

                $itemVenda->save();
            }
            // /tabela de produto vendas

            // tabela de parcelas
            if ($request->qtde_parcelas > 0 && $request->parcelado) {
                $vencimentoParcela = new \DateTime();
                $vencimentoParcela->modify('+1 month');
                for ($i = 0; $i < $request->qtde_parcelas; $i++) {
                    $parcela = new Parcela();
                    $parcela->venda_id = $venda->id;
                    $parcela->data_vencimento = $vencimentoParcela;
                    
                    // deve ser possivel adicionar juros sobre o valor da parcela, no futuro
                    $parcela->valor_parcela = $request->total_venda / $request->qtde_parcelas;
                    $parcela->save();
    
                    $vencimentoParcela->modify('+1 month');
                }
            }
            // /tabela de parcelas

            DB::commit();

            return response()->json($venda, 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json('Erro ao inserir registro de venda: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venda $venda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendaRequest $request, Venda $venda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        //
    }
}
