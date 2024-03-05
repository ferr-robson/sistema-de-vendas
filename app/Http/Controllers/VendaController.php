<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Parcela;
use App\Models\ItemVenda;
use App\Models\Produto;
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
        $vendas = Venda::with('itens')->with('parcelas')->get();

        return response()->json($vendas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // validar dados de entrada
            $request->validate([
                'cliente' => 'nullable|exists:clientes,id',
                'forma_pagamento' => 'required|exists:forma_pagamentos,id',
                'total_venda' => 'required|numeric|min:0',
                'parcelado' => 'boolean',
                'produtos' => 'required|array', 
                'produtos.*.produto_id' => 'required|exists:produtos,id', 
                'produtos.*.quantidade' => 'required|numeric',
                'qtde_parcelas' => 'nullable|numeric|min:0',
            ]);
        
            // iniciar a transacao
            DB::beginTransaction();
        
            // Preencher dados da tabela de Venda
            $venda = new Venda();
            $venda->fill([
                'cliente_id' => $request->cliente,
                'forma_pagamento_id' => $request->forma_pagamento,
                'data_venda' => now(),
                'total_venda' => $request->total_venda,
                'parcelado' => $request->parcelado,
            ]);
            $venda->save();
        
            // Preencher dados da tebela de ProdutoVenda
            foreach ($request->produtos as $produto) {
                $item = Produto::findOrFail($produto['produto_id']);
        
                $itemVenda = new ItemVenda();
                $itemVenda->fill([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto['produto_id'],
                    'quantidade' => $produto['quantidade'],
                    'preco' => $item->preco * $produto['quantidade'],
                ]);
                $itemVenda->save();
            }
        
            // Preencher dados da tabela de Parcelas, se necessário
            if ($request->parcelado && $request->qtde_parcelas > 0) {
                $vencimentoParcela = now()->addMonth();
        
                for ($i = 0; $i < $request->qtde_parcelas; $i++) {
                    $parcela = new Parcela();
                    $parcela->fill([
                        'venda_id' => $venda->id,
                        'data_vencimento' => $vencimentoParcela,
                        'valor_parcela' => $request->total_venda / $request->qtde_parcelas,
                    ]);
                    $parcela->save();
        
                    $vencimentoParcela->addMonth();
                }
            }
        
            // se houve sucesso, commitar a transacao
            DB::commit();

            return response()->json($venda, 201);
        } catch (\Exception $e) {
            // se houve erro, dar rollback na transacao
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
        $venda->delete();

        return response()->json('Registro de venda removido com sucesso.', 200);
    }
}
