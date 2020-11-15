<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => 1, 'name' => 'latte', 'price' => 1000, 'options' => [1, 5]],
            ['id' => 2, 'name' => 'cappuccino', 'price' => 2000, 'options' => [2, 5]],
            ['id' => 3, 'name' => 'espresso', 'price' => 3000, 'options' => [3, 5]],
            ['id' => 4, 'name' => 'tea', 'price' => 4000, 'options' => [5]],
            ['id' => 5, 'name' => 'hot chocolate', 'price' => 5000, 'options' => [2, 5]],
            ['id' => 6, 'name' => 'cookie', 'price' => 6000, 'options' => [4, 5]],
        ];
        foreach( $items as $item ) {
            $product = Product::updateOrCreate( ['id' => $item['id']], [
                'name' => $item['name'], 'price' => $item['price'],
            ] );
            foreach( $item['options'] as $optionId ) {
                $product->addOption( Option::where( 'id', $optionId )->first() );
            }
        }
    }
}
