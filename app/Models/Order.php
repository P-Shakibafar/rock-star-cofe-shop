<?php

namespace App\Models;

use App\Mail\OrderStatusUpdateMail;
use Illuminate\Support\Facades\Mail;
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

    protected static function boot()
    {
        parent::boot();
        static::updating( function ( $order ) {
            $oldData = $order->getOriginal();
            if( $order['status'] != $oldData['status'] ) {
                Mail::to( $order->user->email )->queue( new OrderStatusUpdateMail( $order->status ) );
            }
        } );
    }

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

    public function canBeUpdate() : bool
    {
        return $this->status === self::WAITING;
    }
}
