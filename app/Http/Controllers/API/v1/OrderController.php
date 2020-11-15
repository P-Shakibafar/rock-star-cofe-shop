<?php

namespace App\Http\Controllers\API\v1;

use DB;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Policies\OrderPolicy;
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
            'items'                   => ['required', 'array'],
            'items.*'                 => [
                'quantity'   => ['required', 'integer'],
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'unit_price' => ['required', 'integer'],
                'options'    => ['required', 'array'],
            ],
            'items.*.options.*.name'  => ['required', 'string', 'exists:options,name'],
            'items.*.options.*.value' => ['required', 'string', 'exists:option_values,value'],
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

        return $this->successResponse( OrderResource::make( $order->load(['user','items']) ), Response::HTTP_CREATED );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show( Order $order )
    {
        $this->authorize( OrderPolicy::SHOW, $order );

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
     * @param \App\Models\Order        $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update( Request $request, Order $order )
    {
        $attributes = $request->validate( [
            'status' => 'required|string',
        ] );
        $order->update( ['status' => $attributes['status']] );

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy( Order $order )
    {
        if( !$order->canBeUpdate() ) {
            return $this->errorResponse( 'when order status is not waiting can not delete.', Response::HTTP_BAD_REQUEST );
        }
        $this->authorize( OrderPolicy::DELETE, $order );
        $order->delete();

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }
}
