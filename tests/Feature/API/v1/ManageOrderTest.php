<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use App\Mail\OrderStatusUpdateMail;
use Illuminate\Support\Facades\Mail;
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
    public function unauthenticated_user_cannot_manage_orders()
    {
        $user      = User::factory()->create();
        $order     = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::WAITING] );
        $otherUser = User::factory()->create();
        $this->getJson( route( 'v1.orders.index' ) )
             ->assertStatus( 401 );
        $this->postJson( route( 'v1.orders.store' ), [] )
             ->assertStatus( 401 );
        $this->getJson( route( 'v1.orders.show', $order->id ) )
             ->assertStatus( 401 );
        $this->patchJson( route( 'v1.orders.update', $order->id ), [] )
             ->assertStatus( 401 );
        $this->deleteJson( route( 'v1.orders.destroy', $order->id ) )
             ->assertStatus( 401 );
        $this->actingAs( $otherUser )
             ->getJson( route( 'v1.orders.show', $order->id ) )
             ->assertStatus( 403 );
        $this->actingAs( $user )
             ->patchJson( route( 'v1.orders.update', $order->id ), [] )
             ->assertStatus( 403 );
        $this->actingAs( $otherUser )
             ->deleteJson( route( 'v1.orders.destroy', $order->id ) )
             ->assertStatus( 403 );
    }

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
        $response            = $this->actingAs( User::factory()->create() )
                                    ->postJson( route( 'v1.orders.store' ), $attributes );
        $response->assertStatus( 201 );
        $response->assertJsonStructure( [
            'data' => $this->jsonStructureOrder,
        ] );
        $order = Order::latest()->first();
        $this->assertCount( 3, $order->items );
    }

    /** @test */
    public function a_user_can_see_an_order()
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create( ['user_id' => $user->id] );
        $order->addItem( OrderItem::factory()->raw() );
        $order->addItem( OrderItem::factory()->raw() );
        $response = $this->actingAs( $user )
                         ->getJson( route( 'v1.orders.show', $order->id ) );
        $response->assertStatus( Response::HTTP_OK );
        $response->assertJsonStructure( ['data' => $this->jsonStructureOrder] );
        $response->assertSee( $order->number );
        $this->assertCount( 2, $order->items );
    }

    /** @test */
    public function an_admin_can_change_order_status()
    {
        $order = Order::factory()->create();
        $order->addItem( OrderItem::factory()->raw() );
        $order->addItem( OrderItem::factory()->raw() );
        $response = $this->actingAs( User::factory()->create( ['is_admin' => TRUE] ) )
                         ->patchJson( route( 'v1.orders.update', $order->id ), [
                             'status' => Order::READY,
                         ] );
        $response->assertStatus( Response::HTTP_NO_CONTENT );
        $this->assertEquals( Order::READY, $order->fresh()->status );
    }

    /** @test */
    public function after_change_order_status_send_email_to_user()
    {
        Mail::fake();
        $order = Order::factory()->create();
        $order->addItem( OrderItem::factory()->raw() );
        $order->addItem( OrderItem::factory()->raw() );
        $response = $this->actingAs( User::factory()->create( ['is_admin' => TRUE] ) )
                         ->patchJson( route( 'v1.orders.update', $order->id ), [
                             'status' => Order::READY,
                         ] );
        $response->assertStatus( Response::HTTP_NO_CONTENT );
        Mail::assertQueued( OrderStatusUpdateMail::class );
        $this->assertEquals( Order::READY, $order->fresh()->status );
    }

    /** @test */
    public function a_user_can_delete_own_order_when_status_is_waiting()
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::WAITING] );
        $order->addItem( OrderItem::factory()->raw() );
        $order->addItem( OrderItem::factory()->raw() );
        $response = $this->actingAs( $user )
                         ->deleteJson( route( 'v1.orders.destroy', $order->id ) );
        $response->assertStatus( Response::HTTP_NO_CONTENT );
        $this->assertDatabaseMissing( 'orders', $order->only( 'id' ) );
    }

    /** @test */
    public function a_user_can_not_delete_own_order_when_status_is_not_waiting()
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create( ['user_id' => $user->id, 'status' => Order::READY] );
        $order->addItem( OrderItem::factory()->raw() );
        $order->addItem( OrderItem::factory()->raw() );
        $response = $this->actingAs( $user )
                         ->deleteJson( route( 'v1.orders.destroy', $order->id ) );
        $response->assertStatus( Response::HTTP_BAD_REQUEST );
        $this->assertDatabaseHas( 'orders', $order->only( 'id' ) );
    }
}
