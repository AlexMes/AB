<?php

namespace App\Console\Commands;

use App\Jobs\CollectLeadDestinationStatuses;
use App\LeadDestination;
use Illuminate\Console\Command;

class CollectLeadDestinationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destination:collect-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collects destination statuses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LeadDestination::whereIsActive(true)
            ->each(fn ($destination) => CollectLeadDestinationStatuses::dispatch($destination));

        return 0;
    }
}
