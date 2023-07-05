<?php

namespace App\Facebook\Events\Accounts;

use App\Facebook\Account;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Created implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Fresh attached ads account
     *
     * @var Account
     */
    public $account;

    /**
     * AccountCreated constructor.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array|PrivateChannel
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("App.Profile.{$this->account->profile->id}"),
        ];
    }

    /**
     * Set the event name
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'Account.Created';
    }
}
