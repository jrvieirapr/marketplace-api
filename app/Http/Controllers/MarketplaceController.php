<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Http\Requests\StoreMarketplaceRequest;
use App\Http\Requests\UpdateMarketplaceRequest;

class MarketplaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
          //Pegar a lista do banco
          $marketplace = Marketplace::all();

          //Retornar lista em formato json
          return response()->json(['data' => $marketplace]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketplaceRequest $request)
    {
         // Crie um novo Marketplace
         $marketplace = Marketplace::create($request->all());

         // Retorne o codigo 201
         return response()->json($marketplace, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
       // procure tipo por id
       $marketplace = Marketplace::find($id);

       if (!$marketplace) {
           return response()->json(['message' => 'Marketplace não encontrado'], 404);
       }

       return response()->json($marketplace);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketplaceRequest $request, $id)
    {
        // Procure o tipo pela id
        $marketplace = Marketplace::find($id);

        if (!$marketplace) {
            return response()->json(['message' => 'Marketplace não encontrado'], 404);
        }

        // Faça o update do tipo
        $marketplace->update($request->all());

        // Retorne o tipo
        return response()->json($marketplace);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         // Encontre um tipo pelo ID
         $marketplace = Marketplace::find($id);

         if (!$marketplace) {
             return response()->json(['message' => 'Marketplace não encontrado!'], 404);
         }  

         //Se tiver dependentes deve retornar erro
   
         // Delete the brand
         $marketplace->delete();
 
         return response()->json(['message' => 'Marketplace deletado com sucesso!'], 200);
    }
}
