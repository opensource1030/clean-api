<?php

namespace WA\Events;

class OrderDeniedTransition extends Event
{
    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(\WA\DataStore\Order\Order $order)
    {
        $this->order = $order;
    }
}
