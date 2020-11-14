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
        $orderItem1 = $order->addItem( OrderItem::factory()->raw() );
        $orderItem2 = $order->addItem( OrderItem::factory()->raw() );
        $attributes = [
            'quantity' => 3,
            'options'  => $options = [
                [
                    'name'  => ( $option = Option::factory()->create() )->name,
                    'value' => OptionValue::factory()->create( ['option_id' => $option->id] )->value,
                ],
                [
                    'name'  => $orderItem1->product->options[1]->name,
                    'value' => $orderItem1->product->options[1]->values[0]->value,
                ],
            ],
        ];
        $response   = $this->actingAs( $user )
                           ->patchJson( route( 'v1.orderItems.update', [$order->id, $orderItem1->id] ),
                               $attributes );
        $response->assertStatus( Response::HTTP_NO_CONTENT );
        $this->assertEquals( 3, $orderItem1->fresh()->quantity );
        $this->assertEquals( $options, $orderItem1->fresh()->options );
    }

    /** @test */
    public function a_user_can_not_update_order_item_in_own_order_when_status_is_not_waiting()
    {
        $user       = User::factory()->create();
        $order      = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::READY] );
        $orderItem1 = $order->addItem( OrderItem::factory()->raw() );
        $response   = $this->actingAs( $user )
                           ->patchJson( route( 'v1.orderItems.update', [$order->id, $orderItem1->id] ),
                               [] );
        $response->assertStatus( Response::HTTP_BAD_REQUEST );
    }
}
