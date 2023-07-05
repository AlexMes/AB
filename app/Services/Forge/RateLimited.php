<?php

namespace App\Services\Forge;

use Illuminate\Support\Facades\Redis;

class RateLimited
{
    /**
     * Process the queued job.
     *
     * @param mixed    $job
     * @param callable $next
     *
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     *
     * @return mixed
     */
    public function handle($job, $next)
    {
        Redis::throttle('forge-api')
            ->block(0)->allow(30)->every(60)
            ->then(function () use ($job, $next) {
                $next($job);
            }, function () use ($job) {
                $job->release(15);
            });
    }
}
