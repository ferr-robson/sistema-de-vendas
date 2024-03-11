<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\ItemVenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                'vendedor_id' => Auth::user()->id,
                'forma_pagamento_id' => $request->forma_pagamento,
                'data_venda' => now(),
                'total_venda' => $request->total_venda,
                'parcelado' => $request->parcelado,
            ]);
            $venda->save();
        
            $validacaoPrecoTotal = 0;
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

                $validacaoPrecoTotal += $item->preco * $produto['quantidade'];

                $itemVenda->save();
            }

            // rollback da transacao se for verificado que o valor informado e meno que a soma dos valores dos produtos
            if (abs($validacaoPrecoTotal - (double)$venda->total_venda) > 0.001) {
                DB::rollback();

                return response()->json('ERRO: Valor total da venda é incompatível.', 400);
            }
        
            // Preencher dados da tabela de Parcelas, se necessário
            if ($request->parcelado) {
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
        $venda->load('itens', 'parcelas');

        return response()->json($venda, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        try {
            $dadosValidados = $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'forma_pagamento_id' => 'exists:forma_pagamentos,id',
                'total_venda' => 'numeric|min:0',
                'parcelado' => 'boolean',
                'qtde_parcelas' => 'numeric|min:0',
            ]);
            
            DB::beginTransaction();

            // caso decisa-se parcelar a compra agora
            if ($request->parcelado && ($request->parcelado != $venda->parcelado)) {
                $vencimentoParcela = $venda->data_venda;
            
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
    
            // se a compra foi inicialmente parcelada e agora eh a vista
            if (!$request->parcelado && ($request->parcelado != $venda->parcelado)) {
                Parcela::where('venda_id', $venda->id)->delete();
            }
    
            $venda->update($dadosValidados);
    
            DB::commit();

            return response()->json($venda, 200);
        } catch (\Exception $e) {
            // se houve erro, dar rollback na transacao
            DB::rollback();
        
            return response()->json('Erro ao inserir registro de venda: ' . $e->getMessage(), 500);
        }
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
