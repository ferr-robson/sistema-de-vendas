<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\ItemVenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreItemVendaRequest;
use App\Http\Requests\UpdateItemVendaRequest;

class ItemVendaController extends Controller
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
        try {
            $request->validate([
                'produto_id' => 'required|exists:produtos,id',
                'venda_id' => 'required|exists:vendas,id',
                'qtde_produtos' => 'numeric|min:1',
            ]);
            
            DB::beginTransaction();
    
            $produto = Produto::findOrFail($request->produto_id);
            
            $itemVenda = new ItemVenda();
            $itemVenda->produto_id = $request->produto_id;
            $itemVenda->venda_id = $request->venda_id;
            $itemVenda->quantidade = $request->qtde_produtos;
            $itemVenda->preco = $request->qtde_produtos * $produto->preco;
            $itemVenda->save();
    
            $venda = Venda::findOrFail($request->venda_id);
            $venda->total_venda += $request->qtde_produtos * $produto->preco;
            $venda->update();
    
            DB::commit();
    
            return response()->json($itemVenda, 201);
        } catch (\Exception $e) {
            DB::rollback();
        
            return response()->json('Erro ao inserir o produto na lista: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemVenda $itemVenda)
    {
        return response()->json($itemVenda, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemVenda $itemVenda)
    {
        try {
            DB::beginTransaction();
            
            $request->validate([
                'produto_id' => 'required|exists:produtos,id',
                'qtde_produtos' => 'numeric|min:1',
            ]);
            // alterar produto id
            if ($itemVenda->produto_id != $request->produto_id) {
                $produto = Produto::findOrFail($request->produto_id);
    
                $venda = Venda::findOrFail($itemVenda->venda_id);
                $venda->total_venda -= $itemVenda->preco;
                $venda->total_venda += $request->qtde_produtos * $produto->preco;
    
                $itemVenda->produto_id = $request->produto_id;
                $itemVenda->quantidade = $request->qtde_produtos;
                $itemVenda->preco = $request->qtde_produtos * $produto->preco;
    
                $venda->update();
                $itemVenda->update();

                DB::commit();
                
                return response()->json('O item da lista de compras foi atualizado com sucesso', 200);
            } else {
                $venda = Venda::findOrFail($itemVenda->venda_id);
                $venda->total_venda -= $itemVenda->preco;
                $venda->total_venda += $request->qtde_produtos * ($itemVenda->preco / $itemVenda->quantidade);

                $itemVenda->preco = $request->qtde_produtos * ($itemVenda->preco / $itemVenda->quantidade);
                $itemVenda->quantidade = $request->qtde_produtos;
    
                $venda->update();
                $itemVenda->update();

                DB::commit();
                
                return response()->json('O item da lista de compras foi atualizado com sucesso', 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
        
            return response()->json('Erro ao atualizar o item da lista de compras: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemVenda $itemVenda)
    {
        try {
            DB::beginTransaction();

            $venda = Venda::findOrFail($itemVenda->venda_id);
            $venda->total_venda -= $itemVenda->preco;
            $venda->update();
            $itemVenda->delete();
            
            DB::commit();

            return response()->json('O item foi removido da lista de compras com sucesso.', 200);
        } catch (\Exception $e) {
            DB::rollback();
        
            return response()->json('Erro ao remover o item da lista de compras: ' . $e->getMessage(), 500);
        }
    }
}
