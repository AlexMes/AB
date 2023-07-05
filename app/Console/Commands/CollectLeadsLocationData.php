<?php

namespace App\Console\Commands;

use App\Jobs\Leads\FetchIpAddressData;
use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CollectLeadsLocationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:collect-ips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $progress = $this->output->createProgressBar(Lead::count());

        foreach (Lead::cursor() as $lead) {
            try {
                FetchIpAddressData::dispatchNow($lead);
            } catch (\Throwable $exception) {
                Log::debug('Cant check ip ' . $lead->ip);
            }
            $progress->advance();
        }

        $progress->finish();
    }
}
