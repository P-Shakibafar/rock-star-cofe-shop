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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order        $order
     * @param \App\Models\OrderItem    $orderItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, Order $order, OrderItem $orderItem )
    {
        if( !$order->canBeUpdate() ) {
            return $this->errorResponse( 'when order status is not waiting can not updating.', Response::HTTP_BAD_REQUEST );
        }
        $attributes = $request->validate( [
            'quantity'        => ['sometimes', 'required', 'integer'],
            'options'         => ['sometimes', 'required', 'array'],
            'options.*.name'  => ['required', 'string', 'exists:options,name'],
            'options.*.value' => ['required', 'string', 'exists:option_values,value'],
        ] );
        $orderItem->update( $attributes );

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order     $order
     * @param \App\Models\OrderItem $orderItem
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy( Order $order, OrderItem $orderItem )
    {
        if( !$order->canBeUpdate() ) {
            return $this->errorResponse( 'when order status is not waiting can not deleting.', Response::HTTP_BAD_REQUEST );
        }
        $orderItem->delete();

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

}
