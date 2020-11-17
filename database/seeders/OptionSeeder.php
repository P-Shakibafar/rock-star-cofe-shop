<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['id' => 1, 'name' => 'milk', 'values' => ['skim', 'semi', 'whole']],
            ['id' => 2, 'name' => 'size', 'values' => ['small', 'medium', 'large']],
            ['id' => 3, 'name' => 'shots', 'values' => ['single', 'double', 'triple']],
            ['id' => 4, 'name' => 'kind', 'values' => ['chocolate chip', 'ginger']],
            ['id' => 5, 'name' => 'consume location', 'values' => ['take away', 'in shop']],
        ];
        foreach( $items as $item ) {
            $option = Option::updateOrCreate( ['id' => $item['id']], ['name' => $item['name']] );
            $option->addValues( $item['values'] );
        }
    }
}
