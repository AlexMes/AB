<?php

namespace App\Jobs;

use App\DestinationDrivers\CallResult;
use App\LeadDestination;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProcessCallResults implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Amount of allowed attempts
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Concerned destination
     *
     * @var \App\LeadDestination
     */
    protected LeadDestination $destination;

    /**
     * Concerned call result
     *
     * @var \Illuminate\Support\Collection
     */
    protected Collection $results;

    /**
     * Concerned assignments
     *
     * @var \Illuminate\Support\Collection
     */
    protected Collection $assignments;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeadDestination $destination, Collection $results)
    {
        $this->destination = $destination;
        $this->results     = $results;
        $this->assignments = collect();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->assignments = $this->destination->assignments()->whereIn('external_id', $this->results->map(fn (CallResult $result) => $result->getId()))->get();
        $this->results->each(fn (CallResult $result) => $this->process($result));
    }

    /**
     * Handle result
     *
     * @param \App\DestinationDrivers\CallResult $result
     *
     * @return void
     */
    protected function process(CallResult $result)
    {
        /** @var LeadOrderAssignment $assignment */
        $assignment = $this->assignments->firstWhere('external_id', $result->getId());

        if ($assignment === null) {
            return;
        }

        if ($result->isDeposit() && !$assignment->hasDeposit() && $assignment->lead->recreate_deposit === true) {
            $assignment->createDeposit($result);
        }

        $newStatus = $this->destination->getInternalStatus($result->getStatus());
        if ($newStatus !== $assignment->status) {
            $assignment->update(['status' => $newStatus]);
        }
    }

    public function tags()
    {
        return ['pulling:'.$this->destination->id,$this->destination->driver];
    }
}
