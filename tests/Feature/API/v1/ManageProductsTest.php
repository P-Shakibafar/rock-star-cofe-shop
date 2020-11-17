<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\OptionValue;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function route;

class ManageProductsTest extends TestCase {

    use RefreshDatabase;

    protected array $jsonStructure    = [
        'id', 'name', 'price',
        'options' => [
            '*' => [
                'id', 'name',
                'values' => [
                    '*' => [
                        'id', 'value',
                    ],
                ],
            ],
        ],
    ];
    protected array $jsonStructureAll = [
        'data' => [
            '*' => [
                'id', 'name', 'price',
                'options' => [
                    '*' => [
                        'id', 'name',
                        'values' => [
                            '*' => [
                                'id', 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /** @test */
    public function unauthenticated_user_cannot_manage_products()
    {
        $product    = Product::factory()->create();
        $normalUser = User::factory()->create();
        $this->getJson( route( 'v1.products.index' ) )
             ->assertStatus( 401 );
        $this->postJson( route( 'v1.products.store' ), [] )
             ->assertStatus( 401 );
        //        $this->getJson( route( 'v1.products.show', $product->id ) )
        //             ->assertStatus( 401 );
        $this->patchJson( route( 'v1.products.update', $product->id ), [] )
             ->assertStatus( 401 );
        $this->deleteJson( route( 'v1.products.destroy', $product->id ) )
             ->assertStatus( 401 );
        $this->actingAs( $normalUser )
             ->postJson( route( 'v1.products.store' ), [] )
             ->assertStatus( 403 );
        //        $this->actingAs( $normalUser )
        //             ->getJson( route( 'v1.products.show', $product->id ) )
        //             ->assertStatus( 403 );
        $this->actingAs( $normalUser )
             ->patchJson( route( 'v1.products.update', $product->id ), [] )
             ->assertStatus( 403 );
        $this->actingAs( $normalUser )
             ->deleteJson( route( 'v1.products.destroy', $product->id ) )
             ->assertStatus( 403 );
    }

    /** @test */
    public function an_admin_or_user_can_see_all_product()
    {
        [$product1, $product2] = Product::factory( 2 )->create();
        $option       = Option::factory()->create();
        $optionValues = Arr::flatten( OptionValue::factory( 2 )->raw() );
        $option->addValues( $optionValues );
        $product1->addOption( $option );
        $product2->addOption( $option );
        $response = $this->actingAs( User::factory()->create() )
                         ->getJson( route( 'v1.products.index' ) );
        $response->assertStatus( Response::HTTP_OK );
        $response->assertJsonStructure( $this->jsonStructureAll );
        $this->assertCount( 2, Product::all() );
    }

    /** @test */
    public function an_admin_can_create_a_product()
    {
        $attributes = Product::factory()->raw();
        $response   = $this->actingAs( User::factory()->create( ['is_admin' => TRUE] ) )
                           ->postJson( route( 'v1.products.store' ), $attributes );
        $response->assertStatus( Response::HTTP_CREATED );
        $response->assertJsonStructure( [
            'data' => $this->jsonStructure,
        ] );
        $this->assertDatabaseHas( 'products', $attributes );
    }

    /** @test */
    public function an_admin_can_updated_a_product()
    {
        $product  = Product::factory()->create();
        $response = $this->actingAs( User::factory()->create( ['is_admin' => TRUE] ) )
                         ->patchJson( route( 'v1.products.update', $product->id ), $data = [
                             'name'  => 'changed',
                             'price' => 12,
                         ] );
        $response->assertStatus( 204 );
        $this->assertDatabaseHas( 'products', $data );
    }

    /** @test */
    public function an_admin_can_delete_a_product()
    {
        $product  = Product::factory()->create();
        $response = $this->actingAs( User::factory()->create( ['is_admin' => TRUE] ) )
                         ->deleteJson( route( 'v1.products.destroy', $product->id ) );
        $response->assertStatus( 204 );
        $this->assertDatabaseMissing( 'products', $product->only( 'id' ) );
    }

    /** @test */
    public function validate_product_request_fields()
    {
        $fields = [
            'name',
            'price',
        ];
        foreach( $fields as $field ) {
            $this->aProductRequires( $field );
        }
    }

    public function aProductRequires( $field )
    {
        $attributes = Product::factory()->raw( [$field => ''] );
        $response   = $this->actingAs( User::factory()->create( ['is_admin' => TRUE] ) )
                           ->postJson( route( 'v1.products.store' ), $attributes );
        $response->assertStatus( 422 );
        $response->assertJsonStructure( ['error' => [$field]] );
    }
}
