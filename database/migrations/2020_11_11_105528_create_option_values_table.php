<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionValuesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'option_values', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'value' )->unique();
            $table->foreignId( 'option_id' )->constrained( 'options' )->cascadeOnDelete();
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
        Schema::dropIfExists( 'option_values' );
    }
}
