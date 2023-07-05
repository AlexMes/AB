<?php

namespace App\Jobs\Leads;

use App\AdsBoard;
use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyOfferForReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Lead|null
     */
    protected ?Lead $lead;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        if (
            data_get($this->lead->requestData, 'domain') !== null
            && $this->lead->affiliate_id === null
            && data_get($this->lead->requestData, 'offer') == null
            && cache()->get(sprintf('report:domains:lock:%s', basename(data_get($this->lead->requestData, 'domain')))) !== 'lock') {
            cache()->put(sprintf('report:domains:lock:%s', basename(data_get($this->lead->requestData, 'domain'))), 'lock', now()->addMinutes(10));

            AdsBoard::report(
                sprintf(
                    'Lead %s received without offer id, domain %s',
                    sprintf('%s %s (%d)', $this->lead->firstname, $this->lead->lastname, $this->lead->phone),
                    data_get($this->lead->requestData, 'domain')
                )
            );
        }
    }
}
