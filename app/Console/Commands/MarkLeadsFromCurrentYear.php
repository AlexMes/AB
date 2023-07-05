<?php

namespace App\Console\Commands;

use App\Jobs\AssignMarkersToLead;
use App\Lead;
use Illuminate\Console\Command;

class MarkLeadsFromCurrentYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mark-leads-from-current-year';

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
        foreach (Lead::where('created_at', '>', now()->startOfYear()->toDateTimeString())->cursor() as $lead) {
            AssignMarkersToLead::dispatchNow($lead);
        }

        return 0;
    }
}
