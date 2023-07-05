<?php

namespace App\Deluge\Events\Accounts;

use App\ManualAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Saved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var ManualAccount
     */
    public ManualAccount $account;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ManualAccount $account)
    {
        $this->account = $account;
    }
}
