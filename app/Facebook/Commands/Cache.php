<?php

namespace App\Facebook\Commands;

use App\Facebook\Account;
use App\Facebook\Profile;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class Cache extends Command
{
    /**
     * The name of the console command
     *
     * @var string
     */
    protected $signature = 'fb:cache
                            {--age : Cache age insights}
                            {--platform : Cache platform insights}
                            {--range : Cache insights for a period of time}
                            {date? : Cache insights for a specific date}';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Cache stats from the facebook';

    /**
     * Execute command
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->hasFilters()) {
            $this->cache();
        }

        if ($this->option('range')) {
            $this->runForPeriod();
        }

        if ($this->argument('date')) {
            $this->cache(Carbon::parse($this->argument('date')));
        }
    }


    /**
     * @param null $date
     */
    public function cache($date = null)
    {
        Account::whereIn('profile_id', $this->getAliveProfilesID())
            ->each(function (Account $account) use ($date) {
                if ($this->option('age')) {
                    $account->cacheAgeInsights($date);
                } elseif ($this->option('platform')) {
                    $account->cachePlatformInsights($date);
                } else {
                    $account->cacheInsights($date);
                }
            });
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
            ->until($this->ask('Until date') ?? now());

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

    /**
     * Get ids for alive profiles
     *
     * @return mixed
     */
    protected function getAliveProfilesID()
    {
        return Profile::issueDoesntExist()->pluck('id');
    }
}
