<?php

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'orders', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'number' )->unique();
            $table->enum( 'status', Order::STATUS_LIST )->default( Order::WAITING );
            $table->foreignId( 'user_id' )->constrained( 'users' )->cascadeOnDelete();
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
        Schema::dropIfExists( 'orders' );
    }
}
