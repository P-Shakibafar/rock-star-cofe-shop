<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function route;

class OptionProductAddingTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_admin_can_add_an_option_to_product()
    {
        $product = Product::factory()->create();
        $option  = Option:: factory()->create();
        $this->postJson( route( 'v1.products.addOption', $product->id ), [
            'name' => $option->name,
        ] )
             ->assertStatus( 201 );
        $this->assertTrue( $product->options->contains( $option ) );
    }

    /** @test */
    public function an_admin_can_remove_an_option_from_product()
    {
        $this->withoutExceptionHandling();
        $product = Product::factory()->create();
        $product->addOption( $option = Option::factory()->create() );
        $this->deleteJson( route( 'v1.products.removeOption', $product->id ), [
            'name' => $option->name,
        ] )
             ->assertStatus( 200 );
        $this->assertFalse( $product->fresh()->options->contains( $option ) );
    }

    /** @test */
    public function the_adding_name_must_be_associated_a_valid_option()
    {
        $product = Product::factory()->create();
        $this->postJson( route( 'v1.products.addOption', $product->id ), [
            'name' => 'notaoption',
        ] )
             ->assertStatus( 422 )
             ->assertJsonStructure( ['errors' => ['name']] );
    }

    /** @test */
    public function the_remove_name_must_be_associated_a_valid_option()
    {
        $product = Product::factory()->create();
        $this->deleteJson( route( 'v1.products.removeOption', $product->id ), [
            'name' => 'notaoption',
        ] )
             ->assertStatus( 422 )
             ->assertJsonStructure( ['errors' => ['name']] );
    }
}
