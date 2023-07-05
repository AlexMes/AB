<?php

namespace App\Jobs;

use App\LeadDestination;
use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CollectLeadDestinationStatuses implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var LeadDestination
     */
    protected LeadDestination $destination;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeadDestination $destination)
    {
        $this->destination = $destination;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            LeadOrderAssignment::whereDestinationId($this->destination->id)
                ->whereNotNull('status')
                ->select(['status'])
                ->distinct('status')
                ->pluck('status')
                ->each(function ($externalStatus) {
                    $exist = array_filter($this->destination->status_map ?? [], fn ($item) => $item['external'] === $externalStatus);
                    if (empty($exist)) {
                        $this->destination->status_map = array_merge(
                            $this->destination->status_map ?? [],
                            [['external' => $externalStatus, 'internal' => '']]
                        );
                    }
                });
        } catch (\Throwable $th) {
            $this->destination->collect_statuses_error = $th->getMessage();
        }

        $this->destination->save();
    }
}
