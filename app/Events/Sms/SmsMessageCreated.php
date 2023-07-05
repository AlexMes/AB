<?php

namespace App\Events\Sms;

use App\SmsMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmsMessageCreated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var SmsMessage
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param SmsMessage $message
     */
    public function __construct(SmsMessage $message)
    {
        $this->message = $message;
    }
}
