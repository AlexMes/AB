<?php

namespace App\Facebook\Events\PaymentFails;

use App\Facebook\PaymentFail;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Created
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var PaymentFail
     */
    public $paymentFail;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PaymentFail $paymentFail)
    {
        $this->paymentFail = $paymentFail;
    }
}
