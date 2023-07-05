<?php

namespace App\Events\Deposits;

use App\Deposit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
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


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Deposits.{$this->deposit->id}");
    }

    /**
     * Set event name
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'Updated';
    }
}
