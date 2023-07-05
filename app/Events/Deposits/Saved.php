<?php

namespace App\Events\Deposits;

use App\Deposit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Saved
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Deposit model
     *
     * @var \App\Deposit
     */
    public $deposit;

    /**
     * Create a new event instance.
     *
     * @param \App\Deposit $deposit
     */
    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }
}
