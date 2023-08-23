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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketplaceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketplaceRequest $request, Marketplace $marketplace)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marketplace $marketplace)
    {
        //
    }
}
