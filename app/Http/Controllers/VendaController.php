<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\ItemVenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVendaRequest;
use App\Http\Requests\UpdateVendaRequest;

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
    public function store(StoreVendaRequest $request)
    {
        try {
            $request->validated();
            
            DB::beginTransaction();
            
            $venda = Venda::create([
                'cliente_id' => $request->cliente,
                'vendedor_id' => Auth::user()->id,
                'forma_pagamento_id' => $request->forma_pagamento,
                'data_venda' => now(),
                'total_venda' => $request->total_venda,
                'parcelado' => $request->parcelado
            ]);
        
            $validacaoPrecoTotal = 0;

            // Preencher dados da tebela de ProdutoVenda
            foreach ($request->produtos as $produto) {
                $item = Produto::findOrFail($produto['produto_id']);
        
                ItemVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto['produto_id'],
                    'quantidade' => $produto['quantidade'],
                    'preco' => $item->preco * $produto['quantidade']
                ]);

                $validacaoPrecoTotal += $item->preco * $produto['quantidade'];
            }

            // verifica se o valor informado e diferente que a soma dos valores dos produtos
            if (abs($validacaoPrecoTotal - (double)$venda->total_venda) > 0.001) {
                DB::rollback();

                return response()->json('ERRO: Valor total da venda é incompatível.', 400);
            }
        
            // Preencher dados da tabela de Parcelas, se necessário
            if ($request->parcelado) {
                $vencimentoParcela = now()->addMonth();
        
                for ($i = 0; $i < $request->qtde_parcelas; $i++) {
                    Parcela::create([
                        'venda_id' => $venda->id,
                        'data_vencimento' => $vencimentoParcela,
                        'valor_parcela' => $request->total_venda / $request->qtde_parcelas,
                    ]);
                            
                    $vencimentoParcela->addMonth();
                }
            }

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
        $venda->load('itens', 'parcelas');

        return response()->json($venda, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendaRequest $request, Venda $venda)
    {
        try {
            DB::beginTransaction();

            $dados = $request->validated();

            // caso decisa-se parcelar a compra agora
            if ($request->parcelado && ($request->parcelado != $venda->parcelado)) {
                $vencimentoParcela = \DateTime::createFromFormat('Y-m-d', $venda->data_venda);
                $vencimentoParcela->add(new \DateInterval('P1M'));
                
                for ($i = 0; $i < $request->qtde_parcelas; $i++) {
                    Parcela::create([
                        'venda_id' => $venda->id,
                        'data_vencimento' => $vencimentoParcela,
                        'valor_parcela' => $request->total_venda / $request->qtde_parcelas,
                    ]);
        
                    $vencimentoParcela->add(new \DateInterval('P1M'));
                }
            }
            
            $venda->update($dados);
            
            // se a compra foi inicialmente parcelada e agora eh a vista
            if (!$request->parcelado && ($request->parcelado != $venda->parcelado)) {
                Parcela::where('venda_id', $venda->id)->delete();
            }

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
