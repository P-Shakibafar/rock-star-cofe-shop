<?php

namespace Tests\Unit\API\v1;

use Tests\TestCase;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function it_can_add_option()
    {
        $product = Product::factory()->create();
        $option  = Option::factory()->create();
        $product->addOption( $option );
        $this->assertTrue( $product->options->contains( $option ) );
    }

    /** @test */
    public function it_can_remove_option()
    {
        $product = Product::factory()->create();
        $product->addOption( $option = Option::factory()->create() );
        $this->assertTrue( $product->fresh()->options->contains( $option ) );
        $product->removeOption( $option );
        $this->assertFalse( $product->fresh()->options->contains( $option ) );
    }

    /** @test */
    public function it_belongs_to_many_option()
    {
        $product = Product::factory()->create();
        $this->assertInstanceOf( Collection::class, $product->options );
    }
}
