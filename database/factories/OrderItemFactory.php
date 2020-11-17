<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\OptionValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use function random_int;

class OrderItemFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        $product = Product::factory()->create();
        $product->addOption( $option = Option::factory()->create() );
        $product->addOption( $option = Option::factory()->create() );

        return [
            'quantity'   => $quantity = random_int( 1, 10 ),
            'product_id' => $product->id,
            'unit_price' => $product->price,
            'total'      => ( $product->price * $quantity ),
            'options'    => [
                [
                    'name'  => $product->options[0]->name,
                    'value' => OptionValue::factory()->create( ['option_id' => $product->options[0]->id] )->value,
                ],
                [
                    'name'  => $product->options[1]->name,
                    'value' => OptionValue::factory()->create( ['option_id' => $product->options[1]->id] )->value,
                ],
            ],
        ];
    }
}
