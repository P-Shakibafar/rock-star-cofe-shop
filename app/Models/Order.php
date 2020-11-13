<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model {

    use HasFactory;

    const STATUS_LIST = [
        self::WAITING, self::PREPARATION, self::READY, self::DELIVERED,
    ];
    const WAITING     = 'waiting';
    const PREPARATION = 'preparation';
    const READY       = 'ready';
    const DELIVERED   = 'delivered';

    protected $guarded = [];

    public function addItem( array $attributes )
    {
        return $this->items()->create( $attributes );
    }

    public function items()
    {
        return $this->hasMany( OrderItem::class );
    }

    public function user()
    {
        return $this->belongsTo( User::class );
    }

    public function removeItem( OrderItem $item )
    {
        return $this->items()->where( 'id', $item->id )->delete();
    }
}
