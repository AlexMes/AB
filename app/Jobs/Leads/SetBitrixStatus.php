<?php

namespace App\Jobs\Leads;

use App\Deposit;
use App\DestinationDrivers\Contracts\GetsInfoFromDestination;
use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetBitrixStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\LeadOrderAssignment
     */
    protected LeadOrderAssignment $assignment;

    /**
    * @var \App\DestinationDrivers\Contracts\GetsInfoFromDestination
    */
    protected GetsInfoFromDestination $destination;

    /**
     * Create a new job instance.
     *
     * @param LeadOrderAssignment     $assignment
     * @param GetsInfoFromDestination $destination
     */
    public function __construct(LeadOrderAssignment $assignment, GetsInfoFromDestination $destination)
    {
        $this->assignment  = $assignment;
        $this->destination = $destination;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = $this->destination->getLead($this->assignment);

        if ($response['STATUS_ID'] === 'Депозит' && !$this->assignment->hasDeposit()) {
            Deposit::createFromAssignment($this->assignment);
        }

        $this->assignment->update([
            'status'  => $response['STATUS_ID'],
            'comment' => $response['COMMENTS'],
        ]);
    }
}
