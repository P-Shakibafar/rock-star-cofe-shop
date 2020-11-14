<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\ApiController;

class OrderItemController extends ApiController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order        $order
     * @param \App\Models\OrderItem    $orderItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, Order $order, OrderItem $orderItem )
    {
        if( $order->canBeUpdate() ) {
            return $this->errorResponse( 'when order status is not waiting can not updating.', Response::HTTP_BAD_REQUEST );
        }
        $attributes = $request->validate( [
            'quantity'        => ['required', 'integer'],
            'options'         => ['required', 'array'],
            'options.*.name'  => ['required', 'string', 'exists:options,name'],
            'options.*.value' => ['required', 'string', 'exists:option_values,value'],
        ] );
        $orderItem->update( [
            'quantity' => $attributes['quantity'],
            'options'  => $attributes['options'],
        ] );

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id )
    {
        //
    }

}
