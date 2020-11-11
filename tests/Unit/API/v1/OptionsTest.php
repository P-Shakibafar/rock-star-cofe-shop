<?php

namespace Tests\Unit\API\v1;

use Tests\TestCase;
use App\Models\Option;
use Illuminate\Support\Arr;
use App\Models\OptionValue;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OptionsTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function it_has_values()
    {
        $option = Option::factory()->create();
        $this->assertInstanceOf( Collection::class, $option->values );
    }

    /** @test */
    public function it_can_add_values()
    {
        $option       = Option::factory()->create();
        $optionValues = Arr::flatten( OptionValue::factory( 3 )->raw() );
        $option->addValues( $optionValues );
        $this->assertEquals( 3, $option->values()->count() );
    }
}
