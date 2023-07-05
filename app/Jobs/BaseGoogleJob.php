<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

abstract class BaseGoogleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function handle()
    {
        Redis::throttle('google')->allow(30)->every(100)->then(function () {
            $this->execute();
        }, function () {
            return $this->release(30);
        });
    }

    /**
     * Retry job run for and hour
     *
     * @return \Carbon\Carbon
     */
    public function retryUntil()
    {
        return now()->addHours(2);
    }

    abstract public function execute();
}
