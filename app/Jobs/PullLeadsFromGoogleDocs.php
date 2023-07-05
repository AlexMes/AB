<?php

namespace App\Jobs;

use App\Events\LeadPulled;
use App\GoogleSheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class PullLeadsFromGoogleDocs implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\GoogleSheet
     */
    protected GoogleSheet $sheet;

    /**
     * Create a new job instance.
     *
     * @param \App\GoogleSheet $sheet
     */
    public function __construct(GoogleSheet $sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * Handle a job
     *
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     *
     * @return void
     *
     */
    public function handle()
    {
        Redis::throttle('google')->allow(30)->every(100)->then(function () {
            try {
                $this->execute();
            } catch (\Throwable $th) {
                logger('Unable to pull result from sheets '.$th->getMessage());
            }
        }, function () {
            $this->release(30);
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function execute()
    {
        $this->sheet->pull()->each(function ($assignment) {
            if ($assignment !== null) {
                try {
                    $assignment->confirmed_at = now();
                    $assignment->save();
                    LeadPulled::dispatch($assignment);
                } catch (\Throwable $exception) {
                    \Log::error(sprintf(
                        "Unable to update data for lead %s, assignment %s",
                        $assignment->lead->id,
                        $assignment->id,
                    ));
                }
            }
        });
    }

    /**
    * Retry job run for two hours
    *
    * @return \Carbon\Carbon
    */
    public function retryUntil()
    {
        return now()->addHours(1);
    }
}
