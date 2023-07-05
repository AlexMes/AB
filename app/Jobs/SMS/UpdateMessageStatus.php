<?php

namespace App\Jobs\SMS;

use App\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class UpdateMessageStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * SMS message
     *
     * @var \App\SmsMessage
     */
    protected $message;

    /**
     * @var int
     */
    protected $delayBetweenTries = 20;

    /**
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @param \App\SmsMessage $message
     *
     * @return void
     */
    public function __construct(SmsMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function handle()
    {
        if ($this->message->hasVendorId() && optional($this->message->campaign->branch)->isSmsServiceValid()) {
            try {
                $status = $this->message->campaign->branch->initializeSmsService()
                    ->getStatus($this->message->getVendorId());
            } catch (Throwable $exception) {
                if ($this->attempts() < $this->tries) {
                    $this->release($this->delayBetweenTries);

                    return;
                }

                throw $exception;
            }

            $this->message->update([
                'status' => $status,
            ]);
        }
    }
}
