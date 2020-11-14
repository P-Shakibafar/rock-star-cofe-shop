<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Option;
use App\Models\OrderItem;
use App\Models\OptionValue;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function route;

class ManageOrderItemTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function a_user_can_update_order_item_in_own_order_when_status_is_waiting()
    {
        $user       = User::factory()->create();
        $order      = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::WAITING] );
        $orderItem  = $order->addItem( OrderItem::factory()->raw() );
        $attributes = [
            'quantity' => $orderItemQuantity = 3,
            'options'  => $options = [
                [
                    'name'  => ( $option = Option::factory()->create() )->name,
                    'value' => OptionValue::factory()->create( ['option_id' => $option->id] )->value,
                ],
                [
                    'name'  => $orderItem->product->options[1]->name,
                    'value' => $orderItem->product->options[1]->values[0]->value,
                ],
            ],
        ];
        $response   = $this->actingAs( $user )
                           ->patchJson( route( 'v1.orderItems.update', [$order->id, $orderItem->id] ),
                               $attributes );
        $response->assertStatus( Response::HTTP_NO_CONTENT );
        $this->assertEquals( $orderItemQuantity, $orderItem->fresh()->quantity );
        $this->assertEquals( $options, $orderItem->fresh()->options );
        $this->assertEquals( ( $orderItemQuantity * $orderItem->product->price ), $orderItem->fresh()->total );
    }

    /** @test */
    public function a_user_can_not_update_order_item_in_own_order_when_status_is_not_waiting()
    {
        $user      = User::factory()->create();
        $order     = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::READY] );
        $orderItem = $order->addItem( OrderItem::factory()->raw() );
        $response  = $this->actingAs( $user )
                          ->patchJson( route( 'v1.orderItems.update', [$order->id, $orderItem->id] ),
                              [] );
        $response->assertStatus( Response::HTTP_BAD_REQUEST );
    }

    /** @test */
    public function a_user_can_delete_order_item_in_own_order_when_status_is_waiting()
    {
        $user      = User::factory()->create();
        $order     = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::WAITING] );
        $orderItem = $order->addItem( OrderItem::factory()->raw() );
        $response  = $this->actingAs( $user )
                          ->deleteJson( route( 'v1.orderItems.destroy', [$order->id, $orderItem->id] ) );
        $response->assertStatus( Response::HTTP_NO_CONTENT );
        $this->assertDatabaseMissing( 'order_items', $orderItem->only( 'id' ) );
    }

    /** @test */
    public function a_user_can_not_delete_order_item_in_own_order_when_status_is_not_waiting()
    {
        $user      = User::factory()->create();
        $order     = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::READY] );
        $orderItem = $order->addItem( OrderItem::factory()->raw() );
        $response  = $this->actingAs( $user )
                          ->deleteJson( route( 'v1.orderItems.destroy', [$order->id, $orderItem->id] ) );
        $response->assertStatus( Response::HTTP_BAD_REQUEST );
        $this->assertDatabaseHas( 'order_items', $orderItem->only( 'id' ) );
    }
}
