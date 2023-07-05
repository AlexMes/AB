<?php

namespace App\Jobs\SMS;

use App\Lead;
use App\SmsCampaign;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Class Message
 *
 * @package App\Jobs\SMS
 */
class Message implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * @var \App\Lead
     */
    private $lead;

    /**
     * @var \App\SmsCampaign
     */
    private $campaign;

    /**
     * @var int
     */
    protected $delayBetweenTries = 20;

    /**
     * @var int
     */
    public $tries = 5;


    /**
     * Message constructor.
     *
     * @param \App\Lead        $lead
     * @param \App\SmsCampaign $campaign
     */
    public function __construct(Lead $lead, SmsCampaign $campaign)
    {
        $this->lead     = $lead;
        $this->campaign = $campaign;
    }

    /**
     * Process a job
     *
     * @throws Throwable
     *
     * @return void
     */
    public function handle()
    {
        if ($this->canSendSMS()) {
            try {
                $response = $this->campaign->branch->initializeSmsService()
                    ->sendOne($this->getTargetPhone(), $this->getTargetText());
            } catch (Throwable $exception) {
                $this->clearLockSendSMS();

                if ($this->attempts() < $this->tries) {
                    $this->release($this->delayBetweenTries);

                    return;
                }

                throw $exception;
            }

            $this->lead->messages()->create([
                'phone'           => $this->getTargetPhone(),
                'message'         => $this->getTargetText(),
                'campaign_id'     => $this->campaign->id,
                'raw_response'    => $response,
            ]);
        }
    }

    /**
     * Get formatted phone number
     *
     * @return string
     */
    protected function getTargetPhone(): string
    {
        return (mb_substr($this->lead->phone, 0, 1) == 8)
            ? substr_replace($this->lead->phone, 7, 0, 1) :
            $this->lead->phone;
    }

    /**
     * Get resulting message text
     *
     * @return string
     */
    protected function getTargetText(): string
    {
        return str_replace('{name}', $this->lead->firstname, $this->campaign->text);
    }

    /**
     * Determine is we bombarding
     * leads with sms
     *
     * @return bool
     */
    protected function canSendSMS()
    {
        return optional($this->campaign->branch)->isSmsServiceValid()
            && Cache::lock($this->getLockStr(), 20 * Carbon::SECONDS_PER_MINUTE)->get();
    }

    /**
     * Clears lock from bombarding
     */
    protected function clearLockSendSMS()
    {
        Cache::forget($this->getLockStr());
    }

    /**
     * @return string
     */
    protected function getLockStr(): string
    {
        return "sms-to-{$this->lead->phone}-" . md5($this->getTargetText());
    }
}
