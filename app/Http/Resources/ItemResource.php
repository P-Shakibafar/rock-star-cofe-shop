<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray( $request )
    {
        return [
            'quantity'   => $this->quantity,
            'unit_price' => $this->unit_price,
            'options'    => $this->options,
            'product'    => $this->product,
        ];
    }
}
