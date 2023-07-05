<?php

namespace App\Console\Commands;

use App\Lead;
use App\LeadAssigner\LeadAssigner;
use Illuminate\Console\Command;

class DistributeLeadsForTrafficon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:send-leads-to-trafficon';

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
        $leads = Lead::leftovers()->where('offer_id', 222)->get();

        $delay = 0;

        foreach ($leads as $lead) {
            LeadAssigner::dispatch($lead, [], [56])->delay(now()->addMinutes($delay));
            $delay += 6;
        }

        return 0;
    }
}
