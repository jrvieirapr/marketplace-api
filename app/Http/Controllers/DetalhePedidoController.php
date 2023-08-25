<?php

namespace App\Http\Controllers;

use App\Models\DetalhePedido;
use App\Http\Requests\StoreDetalhePedidoRequest;
use App\Http\Requests\UpdateDetalhePedidoRequest;

class DetalhePedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalhes = DetalhePedido::all();

        //Retornar lista em formato json
        return response()->json(['data' => $detalhes]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDetalhePedidoRequest $request)
    {
       // Crie um novo Tipo
       $detalhePedido = DetalhePedido::create($request->all());

       // Retorne o codigo 201
       return response()->json($detalhePedido, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // procure tipo por id
        $detalhe = DetalhePedido::find($id);

        if (!$detalhe) {
            return response()->json(['message' => 'Detalhe Pedido não encontrado'], 404);
        }

        return response()->json($detalhe);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDetalhePedidoRequest $request, $id)
    {
        // Procure o tipo pela id
        $detalhe = DetalhePedido::find($id);

        if (!$detalhe) {
            return response()->json(['message' => 'Detalhe Pedido não encontrado'], 404);
        }

        // Faça o update do tipo
        $detalhe->update($request->all());

        // Retorne o tipo
        return response()->json($detalhe);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Encontre um tipo pelo ID
        $detalhe = DetalhePedido::find($id);



        if (!$detalhe) {
            return response()->json(['message' => 'Detalhe Pedido não encontrado!'], 404);
        }  

        //Se tiver dependentes deve retornar erro
  
        // Delete the brand

        $detalhe->delete();

        return response()->json(['message' => 'Detalhe Pedido deletado com sucesso!'], 200);
    }
}
