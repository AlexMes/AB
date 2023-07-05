<?php

namespace App\Binom\Commands;

use App\Binom\Campaign;
use App\Binom\Jobs\CacheCampaignStatistics;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CacheCampaignStats extends \Illuminate\Console\Command
{

    /**
     * The name of the console command
     *
     * @var string
     */
    protected $signature = 'binom:cache:stats
                            {--range : Cache insights for a period of time}
                            {date? : Cache insights for a specific date}';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Cache binom campaigns statistics';


    /**
     * Execute command
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->hasFilters()) {
            $this->cache(now());
        }

        if ($this->option('range')) {
            $this->runForPeriod();
        }

        if ($this->argument('date')) {
            $this->cache(Carbon::parse($this->argument('date')));
        }
    }

    /**
     * Cache a piece of stats
     *
     * @param \Carbon\Carbon $date
     *
     * @return void
     */
    public function cache($date)
    {
        $campaigns = Campaign::whereHas('binom', function ($query) {
            return $query->where('enabled', true);
        })->whereNotNull('offer_id')->cursor();

        foreach ($campaigns as $campaign) {
            CacheCampaignStatistics::dispatchNow($campaign, $date);
        }
    }

    /**
     * Run caching for a period
     *
     * @return void
     */
    protected function runForPeriod()
    {
        $period = (new CarbonPeriod())
            ->days(1)
            ->since($this->ask('Since date'))
            ->until($this->ask('Until date') ?? now()->addDay());

        foreach ($period as $date) {
            $this->cache($date);
        }
    }

    /**
     * Determine when user provides additional filters
     *
     * @return bool
     */
    protected function hasFilters()
    {
        return $this->option('range') === true || $this->argument('date') !== null;
    }
}
