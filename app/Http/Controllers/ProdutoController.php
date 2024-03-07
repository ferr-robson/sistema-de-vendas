<?php

namespace App\Http\Controllers;

use App\Models\Produto;
// use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::all();

        return response()->json($produtos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255',
            'preco' => 'required|numeric|gt:0'
        ]);

        $produto = new Produto();
        $produto->fill([
            'nome' => $request->nome,
            'preco' => $request->preco,
        ]);
        $produto->save();

        
        return response()->json($produto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        return response()->json($produto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'nome' => 'required|max:255',
            'preco' => 'required|numeric|gt:0'
        ]);

        $produto->nome = $request->nome;
        $produto->preco = $request->preco;
        $produto->update();

        return response()->json($produto, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return response()->json('O produto foi removido com sucesso.', 200);
    }
}
