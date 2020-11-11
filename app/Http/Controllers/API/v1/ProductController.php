<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\ApiController;

class ProductController extends ApiController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::all();

        return $this->successResponse( $products );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Request $request )
    {
        $attributes = $request->validate( [
            'name'  => 'required|string',
            'price' => 'required|regex:/^\d*(\.\d{2})?$/',
        ] );
        $product    = Product::create( $attributes );

        return $this->successResponse( $product, Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show( Product $product )
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit( Product $product )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product      $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, Product $product )
    {
        $attributes = $request->validate( [
            'name'  => 'sometimes|required|string',
            'price' => 'sometimes|required|regex:/^\d*(\.\d{2})?$/',
        ] );
        $product->update( $attributes );

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy( Product $product )
    {
        $product->delete();

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }
}
