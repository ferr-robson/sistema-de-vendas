<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Parcela;
use Illuminate\Http\Request;
use App\Http\Requests\StoreParcelaRequest;
use App\Http\Requests\UpdateParcelaRequest;

class ParcelaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('venda')) {
            $venda = Venda::findOrFail($request->venda);

            return response()->json($venda->parcelas, 200);
        }

        $parcelas = Parcela::all();

        return response()->json($parcelas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParcelaRequest $request)
    {
        $parcela = Parcela::create($request->validated());

        return response()->json($parcela, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Parcela $parcela)
    {
        return response()->json($parcela, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParcelaRequest $request, Parcela $parcela)
    {
        $parcela->update($request->validated());

        return response()->json($parcela, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parcela $parcela)
    {
        $parcela->delete();

        return response()->json('Registro de parcela removido com sucesso', 200);
    }
}
