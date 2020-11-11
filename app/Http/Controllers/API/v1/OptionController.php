<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\ApiController;

class OptionController extends ApiController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $options = Option::all();

        return $this->successResponse( $options );
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
            'name'     => 'required|string',
            'values'   => 'required|array',
            'values.*' => 'string',
        ] );
        $option     = Option::create( ['name' => $attributes['name']] );
        $option->addValues( $attributes['values'] );

        return $this->successResponse( $option->load( 'values' ), Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Option $option
     * @return \Illuminate\Http\Response
     */
    public function show( Option $option )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Option       $option
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, Option $option )
    {
        $attributes = $request->validate( [
            'name'     => 'required|string',
            'values'   => 'required|array',
            'values.*' => 'string',
        ] );
        $option->update( ['name' => $attributes['name']] );
        $option->saveValues( $attributes['values'] );

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Option $option
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy( Option $option )
    {
        $option->delete();

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }
}
