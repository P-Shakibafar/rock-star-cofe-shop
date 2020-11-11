<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function route;
use function array_push;
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
                'id', 'name', 'values' => [
                    '*' => [
                        'value',
                    ],
                ],
            ],
        ],
    ];

    /** @test */
    public function an_admin_can_see_all_option()
    {
        [$option1, $option2, $option3] = Option::factory( 3 )->create();
        $option1->addValues( Arr::flatten( OptionValue::factory( 4 )->raw() ) );
        $option2->addValues( Arr::flatten( OptionValue::factory( 2 )->raw() ) );
        $option3->addValues( Arr::flatten( OptionValue::factory( 3 )->raw() ) );
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

    /** @test */
    public function an_admin_can_updated_a_option()
    {
        $option = Option::factory()->create();
        $option->addValues( Arr::flatten( OptionValue::factory( 4 )->raw() ) );
        $optionValues = Arr::flatten( $option->values()->get( 'value' )->toArray() );
        $deletedValue = $optionValues[0];
        unset( $optionValues[0] );
        array_push( $optionValues, $addedValue = OptionValue::factory()->raw()['value'] );
        $response = $this->patchJson( route( 'v1.options.update', $option->id ), $data = [
            'name'   => 'changed',
            'values' => $optionValues,
        ] );
        $response->assertStatus( 204 );
        $this->assertDatabaseHas( 'options', ['name' => 'changed'] );
        $this->assertFalse( $option->fresh()->values->contains( 'value', $deletedValue ) );
        $this->assertTrue( $option->fresh()->values->contains( 'value', $addedValue ) );
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
