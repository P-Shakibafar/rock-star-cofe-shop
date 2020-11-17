<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Option;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\ApiController;

class ProductOptionController extends ApiController {

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Models\Product      $product
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store( Product $product, Request $request )
    {
        $request->validate( [
            'name' => ['required', 'exists:options,name'],
        ] ,[
            'name.exists' => 'the name you are adding must is a option.',
        ]);
        $option = Option::whereName( $request->name )->first();
        $product->addOption( $option );

        return $this->successResponse( 'add option product', Response::HTTP_CREATED );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product      $product
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( Product $product, Request $request )
    {
        $request->validate( [
            'name' => ['required', 'exists:options,name'],
        ], [
            'name.exists' => 'the name you are adding must is a option.',
        ] );
        $option = Option::whereName( $request->name )->first();
        $product->removeOption( $option );

        return $this->successResponse( 'remove option product', Response::HTTP_OK );
    }
}
