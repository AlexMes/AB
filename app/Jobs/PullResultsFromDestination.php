<?php

namespace App\Jobs;

use App\AdsBoard;
use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\DestinationDrivers\Contracts\CollectsCallResultsByOne;
use App\LeadDestination;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class PullResultsFromDestination implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Amount of allowed attempts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @var int
     */
    public $timeout = 600;

    /**
     * Concerned lead destination
     *
     * @var \App\LeadDestination
     */
    protected LeadDestination $destination;

    /**
     * Start point for results collection
     *
     * @var string|null
     */
    protected $since;

    /**
     * Create a new job instance.
     *
     * @param null|mixed $since
     *
     * @return void
     */
    public function __construct(LeadDestination $leadDestination, $since = null)
    {
        $this->destination = $leadDestination;
        $this->since       = $since;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $handler = $this->destination->initialize();
        if (! $handler instanceof CollectsCallResults && !$handler instanceof CollectsCallResultsByOne) {
            //Destination does not support results collection
            return;
        }

        $since   = Carbon::parse($this->since ?? $this->destination->assignments()->min('created_at'));
        $page    = 1;

        try {
            if ($handler instanceof CollectsCallResultsByOne) {
                $results = $handler->pullResultsByOne($this->destination, $since);
                ProcessCallResults::dispatch($this->destination, $results)->onQueue(AdsBoard::QUEUE_POSTBACKS);
            } else {
                do {
                    /** @var $handler CollectsCallResults */
                    $results = $handler->pullResults($since, $page++);

                    ProcessCallResults::dispatch($this->destination, $results)->onQueue(AdsBoard::QUEUE_POSTBACKS);
                } while ($results->count() !== 0 || !empty($handler->nullInterval));
            }
        } catch (\Throwable $th) {
            AdsBoard::report('Failed to load results from destination' . $this->destination->name . PHP_EOL . $th->getMessage());

            $this->destination->update([
                'collect_results_error' => $th->getMessage(),
            ]);
        }
    }
}
