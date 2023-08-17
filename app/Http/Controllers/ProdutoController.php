<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Pegar a lista do banco
        $produtos = Produto::all();

        //Retornar lista em formato json
        return response()->json(['data' => $produtos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProdutoRequest $request)
    {
        // Crie um novo Tipo
        $produto = Produto::create($request->all());

        // Retorne o codigo 201
        return response()->json($produto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // procure tipo por id
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        return response()->json($produto);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutoRequest $request, $id)
    {
        // Procure o tipo pela id
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        // Faça o update do tipo
        $produto->update($request->all());

        // Retorne o tipo
        return response()->json($produto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       // Encontre um tipo pelo ID
       $produto = Produto::find($id);

       if (!$produto) {
           return response()->json(['message' => 'Produto não encontrado!'], 404);
       }  

       //Se tiver dependentes deve retornar erro
 
       // Delete the brand
       $produto->delete();

       return response()->json(['message' => 'Produto deletado com sucesso!'], 200);
    }
}
