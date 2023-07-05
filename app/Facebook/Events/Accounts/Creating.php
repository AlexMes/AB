<?php

namespace App\Facebook\Events\Accounts;

use App\Facebook\Account;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Creating
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
}
