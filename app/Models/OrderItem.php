<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model {

    use HasFactory;

    protected $casts   = [
        'options' => Json::class,
    ];
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::updating( function ( $orderItem ) {
            $oldItemData = $orderItem->getOriginal();
            if( $orderItem['quantity'] != $oldItemData['quantity'] ) {
                $orderItem['total'] = ( (int)$orderItem['quantity'] * (int)$orderItem['unit_price'] );
            }
        } );
    }

    public function product()
    {
        return $this->belongsTo( Product::class );
    }
}
