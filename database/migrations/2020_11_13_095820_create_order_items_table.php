<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'order_items', function ( Blueprint $table ) {
            $table->id();
            $table->integer( 'quantity' );
            $table->integer( 'unit_price', FALSE,TRUE );
            $table->integer( 'total', FALSE, TRUE );
            $table->json( 'options' )->nullable();
            $table->foreignId( 'product_id' )->constrained( 'products' )->cascadeOnDelete();
            $table->foreignId( 'order_id' )->constrained( 'orders' )->cascadeOnDelete();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'order_items' );
    }
}
