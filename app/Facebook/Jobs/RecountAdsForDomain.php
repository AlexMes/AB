<?php

namespace App\Facebook\Jobs;

use App\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class RecountAdsForDomain
{
    use Dispatchable;
    use Queueable;

    /**
     * @var Domain
     */
    protected Domain $domain;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->domain->total_ads = $this->domain->ads->count();

        $this->domain->passed_ads_count = $this->domain->ads()
            ->whereHas('cachedInsights')
            ->count();

        $this->domain->rejected_ads_count = $this->domain->ads()
            ->whereDoesntHave('cachedInsights')
            ->count();

        $this->domain->saveQuietly();
    }
}
