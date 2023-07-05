<?php

namespace App\Facebook\Listeners;

use App\Facebook\Events\Accounts\Updated;
use App\Facebook\PaymentFail;

class LogPaymentFail
{
    /**
     * Account instance
     *
     * @var \App\Facebook\Account
     */
    protected $account;

    /**
     * Handle the event.
     *
     * @param Updated $event
     *
     * @return void
     */
    public function handle(Updated $event)
    {
        $this->account = $event->account;

        if ($this->paymentFailed()) {
            PaymentFail::query()->create([
                'account_id' => $this->account->account_id,
                'user_id'    => $this->account->user->id,
                'card'       => $this->account->card_number,
            ]);
        }
    }

    /**
     * Determine if updated account has payment fails
     *
     * @return bool
     */
    protected function paymentFailed()
    {
        if ($this->account->isDirty('account_status')) {
            return in_array($this->account->getDirty()['account_status'], [3, 8, 9]);
        }

        return false;
    }
}
