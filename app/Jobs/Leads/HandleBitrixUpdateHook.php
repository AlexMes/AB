<?php

namespace App\Jobs\Leads;

use App\DestinationDrivers\Bitrix24;
use App\DestinationDrivers\Contracts\GetsInfoFromDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleBitrixUpdateHook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var LeadOrderAssignment
     */
    protected LeadOrderAssignment $assignment;

    /**
     * Create a new job instance.
     *
     * @param LeadOrderAssignment $assignment
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Bitrix24|null */
        $bitrix = $this->initDestination();

        if (! is_null($bitrix)) {
            SetBitrixStatus::withChain([
                new SetBitrixComments($this->assignment, $bitrix)
            ])
                ->dispatch($this->assignment, $bitrix);

            return;
        }

        Log::warning(sprintf(
            "Lead %s (assignment: %s) has wrong destination. Incoming postback was from bitrix24",
            $this->assignment->lead->id,
            $this->assignment->id,
        ));
    }

    /**
     * @return GetsInfoFromDestination|null
     */
    protected function initDestination()
    {
        $destination = $this->assignment->route->destination;

        if (optional($destination)->driver === LeadDestinationDriver::B24) {
            return $destination->initialize();
        }

        return null;
    }
}
