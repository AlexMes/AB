<?php

namespace App\Jobs;

use App\LeadAssigner\Checks\EnsureDestinationSheetExists;
use App\LeadAssigner\Checks\EnsureManagerHaveSpreadSheet;
use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class PushLeadToManagerSpreadSheet implements ShouldQueue
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
     * PushLeadToManagerSpreadSheet constructor.
     *
     * @param \App\LeadOrderAssignment $assignment
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Handle the event.
     *
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     *
     * @return void
     */
    public function handle()
    {
        Redis::throttle('gdoc-assignments')
            ->allow(30)
            ->every(60)
            ->then(function () {
                app(Pipeline::class)
                    ->send($this->assignment)
                    ->through([
                        EnsureManagerHaveSpreadSheet::class,
                        EnsureDestinationSheetExists::class,
                    ])
                    ->then(fn ($assignment) => AppendLeadToSheet::dispatch($assignment));
            }, function () {
                logger(sprintf('Throttle: %s', $this->assignment->id), ['gsheets']);
                $this->release(20);
            });
    }

    /**
    * Retry job run for and hour
    *
    * @return \Carbon\Carbon
    */
    public function retryUntil()
    {
        return now()->addHour();
    }
}
