<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray( $request )
    {
        return [
            'number' => $this->number,
            'status' => $this->status,
            'user'   => $this->user,
            'items'  => ItemResource::collection( $this->items ),
        ];
    }
}
