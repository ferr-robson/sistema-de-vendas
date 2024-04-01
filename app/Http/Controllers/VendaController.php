<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Venda;
use App\Models\Parcela;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVendaRequest;
use App\Http\Requests\UpdateVendaRequest;
use Illuminate\Database\Eloquent\Collection;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendas = Venda::with('itens')->with('parcelas')->with('vendedor')->with('forma_pagamento')->get();

        return response()->json($vendas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendaRequest $request)
    {
        $venda = Venda::make($request->validated());
        $venda->setAttribute('vendedor_id', Auth::user()->id);

        // Preco total a ser pago pela compra
        $precoTotalVenda = 0;
        
        // Lista de itens da venda
        $produtoVenda = [];
        
        // Monta o vetor do attach em `$produtoVenda` a partir do `$request->produtos`
        $produtos = collect($request->produtos);
        $produtos->each(function ($produto) use (&$produtoVenda, &$precoTotalVenda) {
            $item = Produto::findOrFail($produto['produto_id']);
            
            $precoTotalProdutoVenda = $item->preco * $produto['quantidade'];

            $produtoVenda += [
                $produto['produto_id'] => [
                    'quantidade' => $produto['quantidade'], 
                    'preco' => $precoTotalProdutoVenda
                    ]
                ];

            $precoTotalVenda += $precoTotalProdutoVenda;
        });

        $request->merge(['total_venda' => $precoTotalVenda]);
        $venda->setAttribute('total_venda', $precoTotalVenda);

        if ((boolean)$venda->parcelado) {
            $parcelas = collect();

            $vencimentoParcela = now();
        
            $parcelas = Collection::times($request->qtde_parcelas, 
                function ($index) use ($vencimentoParcela, $request) {
                    return new Parcela([
                        'data_vencimento' => $vencimentoParcela->copy()->addMonths($index),
                        'valor_parcela' => $request->total_venda / $request->qtde_parcelas,
                    ]);
                });
        }

        try {
            DB::beginTransaction();

            $venda->save();
            $venda->parcelado ? $venda->parcelas()->saveMany($parcelas) : null;
            $venda->itens()->attach($produtoVenda);

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
        $venda->load('itens', 'parcelas', 'vendedor', 'forma_pagamento');

        return response()->json($venda, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendaRequest $request, Venda $venda)
    {
        $vendaAtualizada = $request->validated();
        
        $parcelas = collect();

        // caso decida-se parcelar a compra agora
        if ($request->parcelado && ($request->parcelado != (boolean)$venda->parcelado)) {

            $vencimentoParcela = Carbon::parse($venda->data_venda);
            $request->merge(['total_venda' => $venda->total_venda]);

            $parcelas = Collection::times($request->qtde_parcelas, 
                function ($index) use ($vencimentoParcela, $request) {
                    return new Parcela([
                        'data_vencimento' => $vencimentoParcela->copy()->addMonths($index),
                        'valor_parcela' => $request->total_venda / $request->qtde_parcelas,
                    ]);
                });
        }

        try {
            DB::beginTransaction();

            // se a compra foi inicialmente parcelada e agora eh a vista
            if (!$request->parcelado && ($request->parcelado != (boolean)$venda->parcelado)) {
                Parcela::where('venda_id', $venda->id)->delete();
            }

            $venda->update($vendaAtualizada);
            $request->parcelado ? $venda->parcelas()->saveMany($parcelas) : null;

            DB::commit();

            return response()->json($venda, 200);
        } catch (\Exception $e) {
            DB::rollback();
        
            return response()->json('Erro ao atualizar registro de venda: ' . $e->getMessage(), 500);
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
