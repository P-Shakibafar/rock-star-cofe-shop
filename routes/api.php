<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\ProductController;

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
    Route::get( 'products', [ProductController::class, 'index'] )->name( 'products.index' );
    Route::post( 'products', [ProductController::class, 'store'] )->name( 'products.store' );
    Route::patch( 'products/{product}', [ProductController::class, 'update'] )->name( 'products.update' );
    Route::delete( 'products/{product}', [ProductController::class, 'destroy'] )->name( 'products.destroy' );
} );
