<?php

namespace App\Facebook\Commands;

use App\Facebook\Account;
use App\Facebook\Profile;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CacheProfiles extends Command
{
    /**
        * The name of the console command
        *
        * @var string
        */
    protected $signature = 'fb:cache-profiles
                            {--profile= : Profile ID to cache}
                            {--user= : User ID to cache all profiles}
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
        $accounts = Account::query();

        if ($this->option('user') !== null) {
            $accounts->whereIn('profile_id', Profile::where('user_id', $this->option('user'))->pluck('id'));
        }

        if ($this->option('profile') !== null) {
            $accounts->where('profile_id', $this->option('profile'));
        }

        $accounts->each(function (Account $account) use ($date) {
            $this->info(sprintf("Caching [%s] %s", $date ?? now()->toDateString(), $account->name));
            $account->cacheInsights($date);
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
}
