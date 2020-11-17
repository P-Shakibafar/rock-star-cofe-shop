<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusUpdateMail extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public string $orderStatus;

    /**
     * Create a new message instance.
     *
     * @param $orderStatus
     */
    public function __construct( $orderStatus )
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( 'rockStar@support.com' )
                    ->subject( 'order status changed.' )
                    ->markdown( 'emails.orderStatusUpdate' );
    }
}
