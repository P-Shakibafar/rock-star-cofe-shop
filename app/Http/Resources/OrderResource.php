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
            'id'     => $this->id,
            'number' => $this->number,
            'status' => $this->status,
            'user'   => UserResource::make( $this->whenLoaded( 'user' ) ),
            'items'  => ItemResource::collection( $this->whenLoaded( 'items' ) ),
        ];
    }
}
