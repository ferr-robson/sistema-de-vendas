<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
// use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();

        return response()->json($clientes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255',
            'email' => 'required|email|max:255'
        ]);

        $cliente = new Cliente();
        $cliente->fill([
            'nome' => $request->nome,
            'email' => $request->email
        ]);
        $cliente->save();

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|max:255',
            'email' => 'required|email|max:255'
        ]);

        $cliente->fill([
            'nome' => $request->nome,
            'email' => $request->email
        ]);
        $cliente->update();

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json('Registro de cliente removido com sucesso.', 200);
    }
}
