<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function route;

class ProductsTest extends TestCase {

    use RefreshDatabase;

    protected array $jsonStructure    = [
        'id', 'name', 'price',
    ];
    protected array $jsonStructureAll = [
        'data' => [
            '*' => [
                'id', 'name', 'price',
            ],
        ],
    ];

    /** @test */
    public function an_admin_can_see_all_product()
    {
        Product::factory( 3 )->create();
        $response = $this->getJson( route( 'v1.products.index' ) );
        $response->assertStatus( Response::HTTP_OK );
        $response->assertJsonStructure( $this->jsonStructureAll );
        $this->assertCount( 3, Product::all() );
    }

    /** @test */
    public function an_admin_can_create_a_product()
    {
        $attributes = Product::factory()->raw();
        $response   = $this->postJson( route( 'v1.products.store' ), $attributes );
        $response->assertStatus( Response::HTTP_CREATED );
        $response->assertJsonStructure( [
            'data' => $this->jsonStructure,
        ] );
        $this->assertDatabaseHas( 'products', $attributes );
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
        $response   = $this->postJson( route( 'v1.products.store' ), $attributes );
        $response->assertStatus( 422 );
        $response->assertJsonStructure( ['errors' => [$field]] );
    }
}
