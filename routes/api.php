<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\{OrderController,
    OptionController,
    OrderItemController,
    ProductController,
    ProductOptionController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware( 'auth:api' )->get( '/user', function ( Request $request ) {
    return $request->user();
} );
Route::group( [
    //    'middleware' => 'auth:sanctum',
    'namespace' => 'v1',
    'prefix'    => 'v1',
    'as'        => 'v1.',
], function () {
    // Product Routes
    Route::get( 'products', [ProductController::class, 'index'] )->name( 'products.index' );
    Route::post( 'products', [ProductController::class, 'store'] )->name( 'products.store' );
    Route::patch( 'products/{product}', [ProductController::class, 'update'] )->name( 'products.update' );
    Route::delete( 'products/{product}', [ProductController::class, 'destroy'] )->name( 'products.destroy' );
    // Product option adding Routes
    Route::post( 'products/{product}/addOption', [ProductOptionController::class, 'store'] )->name( 'products.addOption' );
    Route::delete( 'products/{product}/removeOption', [ProductOptionController::class, 'destroy'] )->name( 'products.removeOption' );
    // Product Routes
    Route::get( 'options', [OptionController::class, 'index'] )->name( 'options.index' );
    Route::post( 'options', [OptionController::class, 'store'] )->name( 'options.store' );
    Route::patch( 'options/{option}', [OptionController::class, 'update'] )->name( 'options.update' );
    Route::delete( 'options/{option}', [OptionController::class, 'destroy'] )->name( 'options.destroy' );
    // Order Routes
    Route::get( 'orders', [OrderController::class, 'index'] )->name( 'orders.index' );
    Route::post( 'orders', [OrderController::class, 'store'] )->name( 'orders.store' );
    Route::patch( 'orders/{order}', [OrderController::class, 'update'] )->name( 'orders.update' );
    Route::get( 'orders/{order}', [OrderController::class, 'show'] )->name( 'orders.show' );
    Route::delete( 'orders/{order}', [OrderController::class, 'destroy'] )->name( 'orders.destroy' );
    // Order Item Routes
    Route::patch( 'orders/{order}/orderItems/{orderItem}', [OrderItemController::class, 'update'] )->name( 'orderItems.update' );
    Route::delete( 'orders/{order}/orderItems/{orderItem}', [OrderItemController::class, 'destroy'] )->name( 'orderItems.destroy' );
} );
