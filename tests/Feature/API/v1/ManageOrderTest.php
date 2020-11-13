<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function route;

class ManageOrderTest extends TestCase {

    use RefreshDatabase;

    protected array $jsonStructureOrder                = [
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
    protected array $jsonStructureOrdersWithPagination = [
        'data'  => [
            '*' => [
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
            ],
        ],
        'links' => ['first', 'last', 'prev', 'next'],
        'meta'  => ['current_page', 'last_page', 'from', 'to', 'path', 'per_page', 'total'],
    ];

    /** @test */
    public function a_user_can_see_all_orders()
    {
        $user = User::factory()->create();
        [$order1, $order2] = Order::factory( 2 )->create( ['user_id' => $user->id] );
        $otherOrder = Order::factory()->create();
        $order1->addItem( OrderItem::factory()->raw() );
        $order1->addItem( OrderItem::factory()->raw() );
        $order2->addItem( OrderItem::factory()->raw() );
        $order2->addItem( OrderItem::factory()->raw() );
        $order2->addItem( OrderItem::factory()->raw() );
        $response = $this->actingAs( $user )
                         ->getJson( route( 'v1.orders.index' ) );
        $response->assertStatus( Response::HTTP_OK );
        $response->assertJsonStructure( $this->jsonStructureOrdersWithPagination );
        $response->assertSee( $order1->number );
        $response->assertSee( $order2->number );
        $response->assertDontSee( $otherOrder->number );
    }

    /** @test */
    public function a_user_can_create_an_order()
    {
        $attributes['items'] = OrderItem:: factory( 3 )->raw( ['order_id' => NULL, 'total' => NULL] );
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
