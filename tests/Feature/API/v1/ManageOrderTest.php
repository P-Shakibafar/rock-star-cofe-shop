<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageOrderTest extends TestCase {

    use RefreshDatabase;

    protected array $jsonStructureOrder = [
        'number', 'status',
        'user'  => ['name', 'email'],
        'items' => [
            '*' => [
                'quantity', 'unit_price',
                'options' => [
                    '*' => ['name', 'value'],
                ],
                'product' => ['name', 'price'],
            ],
        ],
    ];

    /** @test */
    public function a_user_can_create_an_order()
    {
        $orderData      = Order::factory()->raw();
        $orderItemsData = OrderItem:: factory( 3 )->raw( ['order_id' => NULL, 'total' => NULL] );
        $attributes     = array_merge( ['order' => $orderData], ['items' => $orderItemsData] );
        $this->actingAs( User::factory()->create() )
             ->postJson( route( 'v1.orders.store' ), $attributes )
             ->assertStatus( 201 )
             ->assertJsonStructure( [
                 'data' => $this->jsonStructureOrder,
             ] );
        $order = Order::latest()->first();
        $this->assertCount( 3, $order->items );
    }
}
