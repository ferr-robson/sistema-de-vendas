<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use App\Http\Requests\StoreFormaPagamentoRequest;
use App\Http\Requests\UpdateFormaPagamentoRequest;

class FormaPagamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formasPagamento = FormaPagamento::all();

        return response()->json($formasPagamento, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormaPagamentoRequest $request)
    {
        $formaPagamento = FormaPagamento::create($request->validated());
     
        return response()->json($formaPagamento, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FormaPagamento $formaPagamento)
    {
        return response()->json($formaPagamento, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormaPagamentoRequest $request, FormaPagamento $formaPagamento)
    {
        $formaPagamento->update($request->validated());

        return response()->json($formaPagamento, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormaPagamento $formaPagamento)
    {
        $formaPagamento->delete();

        return response()->json('Registro de forma de pagamento removido com sucesso.', 200);
    }
}
