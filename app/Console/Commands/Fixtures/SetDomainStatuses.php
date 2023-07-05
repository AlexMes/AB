<?php

namespace App\Console\Commands\Fixtures;

use App\Domain;
use App\Facebook\Jobs\RecountAdsForDomain;
use Illuminate\Console\Command;

class SetDomainStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-domain-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set domain statuses';

    /**
     * Execute the console command.
     *
     * @return mixed
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
    }
}
