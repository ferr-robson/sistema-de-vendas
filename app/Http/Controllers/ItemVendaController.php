<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\ItemVenda;
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
        $itemVenda = ItemVenda::all();

        return response()->json($itemVenda, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemVendaRequest $request)
    {
        try {
            DB::beginTransaction();

            $produto = Produto::findOrFail($request->produto_id);
            
            $dados = $request->validated();
            $dados += ['preco' => $request->quantidade * $produto->preco];
            
            $itemVenda = ItemVenda::create($dados);
    
            $venda = Venda::findOrFail($request->venda_id);
            $venda->total_venda += $itemVenda->preco;
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
    public function update(UpdateItemVendaRequest $request, ItemVenda $itemVenda)
    {
        try {
            DB::beginTransaction();

            $dados = $request->validated();
            $produto_id = $request->produto_id;
            $qtde_produtos = $request->quantidade;
            
            $venda = Venda::findOrFail($itemVenda->venda_id);
            
            if ($itemVenda->produto_id != $produto_id) {
                $produto = Produto::findOrFail($produto_id);
            
                $totalItemVenda = $qtde_produtos * $produto->preco;
            
                $venda->total_venda += $totalItemVenda - $itemVenda->preco;
                $itemVenda->update(array_merge($dados, ['preco' => $totalItemVenda]));
            } else {
                $totalItemVenda = $qtde_produtos * ($itemVenda->preco / $itemVenda->quantidade);
                $venda->total_venda += $totalItemVenda - $itemVenda->preco;
            
                $itemVenda->preco = $totalItemVenda;
                $itemVenda->quantidade = $qtde_produtos;
            }
            
            $venda->update();
            $itemVenda->update();
            
            DB::commit();
            
            return response()->json($itemVenda, 200);
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
