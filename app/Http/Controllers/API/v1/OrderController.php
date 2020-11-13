<?php

namespace App\Http\Controllers\API\v1;

use DB;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\API\ApiController;
use function auth;
use function generateOrderNumber;

class OrderController extends ApiController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $orders = Order::where( 'user_id', auth()->id() )->paginate( 5 );

        return OrderResource::collection( $orders );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store( Request $request )
    {
        $attributes = $request->validate( [
            'items'   => ['required', 'array'],
            'items.*' => [
                'quantity'   => ['required', 'integer'],
                'product_id' => ['required', 'integer', 'exists:products'],
                'unit_price' => ['required', 'regex:/^\d*(\.\d{2})?$/'],
                'options'    => ['required', 'array'],
                'options.*'  => [
                    'name'  => ['string'],
                    'value' => ['string'],
                ],
            ],
        ] );
        $order      = DB::transaction( function () use ( $attributes ) {
            $items    = $attributes['items'];
            $newOrder = auth()->user()->addOrder( [
                'number' => generateOrderNumber(),
                'status' => Order::WAITING,
            ] );
            // store all products for workout
            foreach( $items as $item ) {
                $newOrder->addItem( [
                    'quantity'   => $item['quantity'],
                    'product_id' => $item['product_id'],
                    'unit_price' => $item['unit_price'],
                    'total'      => $item['unit_price'] * $item['quantity'],
                    'options'    => $item['options'],
                ] );
            }

            return $newOrder;
        } );

        return $this->successResponse( OrderResource::make( $order ), Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show( Order $order )
    {
        return $this->successResponse( OrderResource::make( $order ) );
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
     * @param int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        //
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
