<?php

namespace App\Console\Commands\Fixtures;

use App\Jobs\Leads\FetchIpAddressData;
use App\Lead;
use Illuminate\Console\Command;

class LoadIpAddressesForLastThreeMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:load-ip-addresses-for-last-3-month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::valid()->own()->whereNotNull('ip')
            ->whereDoesntHave('ipAddress')
            ->where('ip', '!=', '127.0.0.1')
            ->whereBetween('created_at', [now()->subMonths(3), now()]);

        $progress = $this->output->createProgressBar($leads->count());

        $leads->each(function ($lead) use ($progress) {
            FetchIpAddressData::dispatchNow($lead);
            $progress->advance();
        });

        $progress->finish();

        return 0;
    }
}
