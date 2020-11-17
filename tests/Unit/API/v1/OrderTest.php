<?php

namespace Tests\Unit\API\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function it_has_items()
    {
        $order = Order::factory()->create();
        $this->assertInstanceOf( Collection::class, $order->items );
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $order = Order::factory()->create();
        $this->assertInstanceOf( User::class, $order->user );
    }

    /** @test */
    public function it_can_add_items()
    {
        $order     = Order::factory()->create();
        $orderItem = OrderItem::factory()->raw();
        $order->addItem( $orderItem );
        $this->assertEquals( 1, $order->items()->count() );
    }

    /** @test */
    public function it_can_remove_items()
    {
        $order     = Order::factory()->create();
        $orderItem = OrderItem::factory()->raw();
        $order->addItem( $orderItem );
        $this->assertEquals( 1, $order->items()->count() );
        $order->removeItem( $order->fresh()->items[0] );
        $this->assertEquals( 0, $order->items()->count() );
    }
}
