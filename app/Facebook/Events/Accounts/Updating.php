<?php


namespace App\Facebook\Events\Accounts;

use App\Facebook\Account;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updating
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Account
     */
    public $account;

    /**
     * Updating constructor.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }
}
