<?php

use App\Models\Order;

if( !function_exists( "generateOrderNumber" ) ) {
    function generateOrderNumber()
    {
        $number = mt_rand( 10000, 9999999999 ); // better than rand()
        // call the same function if the username exists already
        if( numberExists( $number ) ) {
            return generateOrderNumber();
        }

        // otherwise, it's valid and can be used
        return $number;
    }

    function numberExists( $number )
    {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return Order::where( 'number', $number )->exists();
    }
}
