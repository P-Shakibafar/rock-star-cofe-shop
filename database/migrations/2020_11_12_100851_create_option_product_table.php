<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionProductTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'option_product', function ( Blueprint $table ) {
            $table->foreignId( 'option_id' );
            $table->foreignId( 'product_id' );
            $table->timestamps();
            $table->index( ['option_id', 'product_id'] );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'option_product' );
    }
}
