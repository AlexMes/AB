<?php

namespace App\Jobs;

use App\Binom;
use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullClickInfo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Lead
     */
    protected $lead;

    /**
     * Create a new job instance.
     *
     * @param \App\Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead->load('click');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->lead->hasAffiliate()) {
            return;
        }

        if ($this->lead->clickid !== null && $this->lead->click === null) {
            $binom = Binom::active()->get()->skipUntil(function (Binom $binom) {
                try {
                    $binom->getClick($this->lead->clickid);

                    return true;
                } catch (Binom\Exceptions\BinomReponseException $exception) {
                    return false;
                }
            })->first();

            if ($binom === null) {
                return null;
            }

            $binomClick = $binom->getClick($this->lead->clickid);

            $click = $this->lead->click()->create(array_merge([
                'binom_id'   => $binom->id,
                'clickid'    => $this->lead->clickid,
                'conversion' => $clickData['conversion'] ?? false
            ], $binomClick));

            $this->lead->addEvent(
                Lead::CLICK_INFO_PULLED,
                ['id' => $click->id, 'clickid' => $this->lead->clickid]
            );

            $this->lead->saveAppIdFromClick($binomClick);
            $this->lead->saveTrafficSourceFromClick($binomClick);
        }
    }
}
