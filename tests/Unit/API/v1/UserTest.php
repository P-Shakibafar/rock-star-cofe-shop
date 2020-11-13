<?php

namespace Tests\Unit\API\v1;

use App\Models\User;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_orders()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf( Collection::class, $user->orders );
    }

    /** @test */
    public function it_can_add_order()
    {
        $user     = User::factory()->create();
        $order = Order::factory()->raw();
        $user->addOrder( $order );
        $this->assertEquals( 1, $user->orders()->count() );
    }

    /** @test */
    public function it_can_remove_order()
    {
        $user     = User::factory()->create();
        $order = Order::factory()->raw();
        $user->addOrder( $order );
        $this->assertEquals( 1, $user->orders()->count() );
        $user->removeOrder( $user->fresh()->orders[0] );
        $this->assertEquals( 0, $user->orders()->count() );
    }
}
