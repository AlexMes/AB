<?php

namespace App\Console\Commands\Fixtures;

use App\Domain;
use App\Facebook\Jobs\RecountAdsForDomain;
use Illuminate\Console\Command;

class RecalculatePrelanderStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:recalculate-prelanders-states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculates prelanders ads stats';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domains = Domain::preLanding();

        $progress = $this->output->createProgressBar($domains->count());

        foreach ($domains->cursor() as $domain) {
            RecountAdsForDomain::dispatchNow($domain);
            $progress->advance();
        }

        $progress->finish();

        return 0;
    }
}
