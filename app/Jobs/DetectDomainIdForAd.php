<?php

namespace App\Jobs;

use App\Domain;
use App\Facebook\Ad;
use App\Facebook\Jobs\RecountAdsForDomain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectDomainIdForAd implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Facebook\Ad
     */
    protected Ad $ad;

    /**
     * Create a new job instance.
     *
     * @param \App\Facebook\Ad $ad
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $domain = Domain::where('url', rtrim($this->ad->creative_url, '/'))->first();

        if ($domain === null) {
            return null;
        }

        if ($domain->id !== $this->ad->domain_id) {
            $oldDomain           = $this->ad->domain;
            $this->ad->domain_id = $domain->id;
            $this->ad->saveQuietly();

            if ($oldDomain !== null) {
                RecountAdsForDomain::dispatchNow($oldDomain);
            }
            RecountAdsForDomain::dispatchNow($domain);
        }
    }
}
