<?php

namespace App\Binom\Commands;

use App\Binom;
use App\Binom\Jobs\CacheCampaignStatistics;
use App\Binom\Jobs\CacheTelegramStatistics;
use App\Binom\Statistic;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckStatistics extends Command
{
    /**
     * Command signature
     *
     * @var string
     */
    protected $signature = 'binom:cache:check
                            {--range : Cache insights for a period of time}
                            {date? : Cache insights for a specific date}';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Check binom caches and rerun caches where it is required';


    /**
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->hasFilters()) {
            $this->check(now());
        }

        if ($this->option('range')) {
            $this->runForPeriod();
        }

        if ($this->argument('date')) {
            $this->check(Carbon::parse($this->argument('date')));
        }
    }

    /**
     * Check cache and rerun it if cache invalid
     *
     * @param \Carbon\CarbonInterface $date
     *
     * @throws \Exception
     */
    protected function check(CarbonInterface $date)
    {
        Binom::active()->where('id', '>', 1)->each(function (Binom $binom) use ($date) {
            $leadsInApp = $this->cachedLeads($binom, $date);
            $leadsInBinom  = $this->binomLeads($binom, $date);
            $this->info(sprintf('Leads in app %d. Leads in Binom %d', $leadsInApp, $leadsInBinom));

            $isOk = $this->task(
                sprintf('[%s] Comparing with binom ', $date->toDateString()),
                fn () => (int) $leadsInApp === (int) $leadsInBinom
            );

            if (! $isOk) {
                $this->task(
                    sprintf('Deleting stats for date %s', $date->toDateString()),
                    fn () =>
                    Statistic::where('binom_id', $binom->id)->where('date', $date->toDateString())->delete()
                );

                $this->task(sprintf("Refreshing date %s", $date->toDateString()), function () use ($date, $binom) {
                    foreach ($binom->campaigns as $campaign) {
                        if ($campaign->ts_id == 5) {
                            retry(20, fn () => CacheTelegramStatistics::dispatchNow($campaign, $date), 200);
                        } else {
                            retry(20, fn () => CacheCampaignStatistics::dispatchNow($campaign, $date), 200);
                        }
                    }
                });

                $leadsInApp = $this->cachedLeads($binom, $date);
                $leadsInBinom  = $this->binomLeads($binom, $date);
                $this->info(sprintf('Leads in app %d. Leads in Binom %d', $leadsInApp, $leadsInBinom));

                $this->task(
                    sprintf('[%s] Checking fresh cache ', $date->toDateString()),
                    fn () => (int) $leadsInApp === (int) $leadsInBinom
                );
            }
        });
    }

    /**
     * @return \Carbon\CarbonPeriod
     */
    public function period()
    {
        return CarbonPeriod::create()
            ->days(1)
            ->since(now()->subMonth())
            ->until(now());
    }

    /**
     * Get number of leads in cache
     *
     * @param \App\Binom              $binom
     * @param \Carbon\CarbonInterface $date
     *
     * @return mixed
     */
    protected function cachedLeads(Binom $binom, CarbonInterface $date)
    {
        return Statistic::where('binom_id', $binom->id)->where('date', $date->toDateString())->sum('leads');
    }

    /**
     * Get count of binom leads
     *
     * @param \App\Binom              $binom
     * @param \Carbon\CarbonInterface $date
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function binomLeads(Binom $binom, CarbonInterface $date)
    {
        return retry(20, fn () => $binom->getTotalLeadsAmount($date), 300);
    }

    /**
     * Check period in time
     *
     * @throws \Exception
     */
    private function runForPeriod()
    {
        $period = (new CarbonPeriod())
            ->days(1)
            ->since($this->ask('Since date'))
            ->until($this->ask('Until date') ?? now());

        foreach ($period as $date) {
            $this->check($date);
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
