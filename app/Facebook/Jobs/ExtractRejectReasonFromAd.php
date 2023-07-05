<?php

namespace App\Facebook\Jobs;

use App\Facebook\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExtractRejectReasonFromAd implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;
    use Queueable;

    protected $ad;

    /**
     * Construct listener
     *
     * @param Ad $ad
     *
     * @return void
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Execute a job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->ad->ad_review_feedback !== null) {
            $this->ad->reject_reason = $this->ad->extractRejectReason($this->ad->ad_review_feedback);

            $this->ad->saveQuietly();
        }
    }
}
