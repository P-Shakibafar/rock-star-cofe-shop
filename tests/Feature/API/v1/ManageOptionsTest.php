<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function dd;
use function route;
use function array_merge;

class ManageOptionsTest extends TestCase {

    use RefreshDatabase;

    protected array $jsonStructure    = [
        'id', 'name', 'values' => [
            '*' => [
                'value',
            ],
        ],
    ];
    protected array $jsonStructureAll = [
        'data' => [
            '*' => [
                'id', 'name',
            ],
        ],
    ];

//    /** @test */
    public function an_admin_can_see_all_option()
    {
        Option::factory( 3 )->create();
        $response = $this->getJson( route( 'v1.options.index' ) );
        $response->assertStatus( Response::HTTP_OK );
        $response->assertJsonStructure( $this->jsonStructureAll );
        $this->assertCount( 3, Option::all() );
    }

    /** @test */
    public function an_admin_can_create_a_option()
    {
        $option       = Option::factory()->raw();
        $optionValues = Arr::flatten( OptionValue::factory( 3 )->raw() );
        $attributes   = array_merge( $option, ['values' => $optionValues] );
        $response     = $this->postJson( route( 'v1.options.store' ), $attributes );
        $response->assertStatus( Response::HTTP_CREATED );
        $response->assertJsonStructure( [
            'data' => $this->jsonStructure,
        ] );
        $this->assertDatabaseHas( 'options', $option );
        $this->assertEquals( 3, Option::latest()->first()->values()->count() );
    }

//    /** @test */
    public function an_admin_can_updated_a_option()
    {
        $option   = Option::factory()->create();
        $response = $this->patchJson( route( 'v1.options.update', $option->id ), $data = [
            'name' => 'changed',
        ] );
        $response->assertStatus( 204 );
        $this->assertDatabaseHas( 'options', $data );
    }

//    /** @test */
    public function an_admin_can_delete_a_option()
    {
        $option   = Option::factory()->create();
        $response = $this->deleteJson( route( 'v1.options.destroy', $option->id ) );
        $response->assertStatus( 204 );
        $this->assertDatabaseMissing( 'options', $option->only( 'id' ) );
    }

//    /** @test */
    public function validate_option_request_fields()
    {
        $fields = [
            'name',
        ];
        foreach( $fields as $field ) {
            $this->aOptionRequires( $field );
        }
    }

    public function aOptionRequires( $field )
    {
        $attributes = Option::factory()->raw( [$field => ''] );
        $response   = $this->postJson( route( 'v1.options.store' ), $attributes );
        $response->assertStatus( 422 );
        $response->assertJsonStructure( ['errors' => [$field]] );
    }
}
