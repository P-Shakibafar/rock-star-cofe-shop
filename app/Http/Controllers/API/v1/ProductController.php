<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ProductResource;
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

        return $this->successResponse( ProductResource::collection( $products ) );
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
            'price' => 'required|integer',
        ] );
        $product    = Product::create( $attributes );

        return $this->successResponse( ProductResource::make( $product->load('options') ), Response::HTTP_CREATED );
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
            'price' => 'sometimes|required|integer',
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
