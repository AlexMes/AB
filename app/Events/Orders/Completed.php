<?php

namespace App\Events\Orders;

use App\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Completed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Order model
     *
     * @var \App\Order
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
