<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

final class OrderPolicy {

    const UPDATE = 'update';
    const SHOW   = 'show';
    const DELETE = 'delete';

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Order $order
     * @return mixed
     */
    public function update( User $user, Order $order )
    {
        return $user->is( $order->user );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Order $order
     * @return mixed
     */
    public function show( User $user, Order $order )
    {
        return $user->is( $order->user );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Order $order
     * @return mixed
     */
    public function delete( User $user, Order $order )
    {
        return $user->is( $order->user );
    }
}
