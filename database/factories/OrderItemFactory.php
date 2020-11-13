<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\OrderItem;
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
     */
    public function definition()
    {
        return [
            'quantity'   => $quantity = random_int( 1, 10 ),
            'product_id' => ( $product = Product::factory()->create() )->id,
            'unit_price' => $product->price,
            'total'      => ( $product->price * $quantity ),
            'options'    => [
                [
                    'name'  => $this->faker->unique()->colorName,
                    'value' => $this->faker->unique()->firstName,
                ],
                [
                    'name'  => $this->faker->unique()->colorName,
                    'value' => $this->faker->unique()->firstName,
                ],
            ],
        ];
    }
}
