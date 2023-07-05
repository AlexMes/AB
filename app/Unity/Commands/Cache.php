<?php

namespace App\Unity\Commands;

use App\AdsBoard;
use App\Unity\Jobs\CollectInsights;
use App\UnityOrganization;
use Illuminate\Console\Command;

class Cache extends Command
{
    /**
     * The name of the console command
     *
     * @var string
     */
    protected $signature = 'unity:cache
                            {--since= : Cache insights since date}
                            {--until= : Cache insights until date}';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Cache stats from the unity';

    /**
     * Execute command
     *
     * @return void
     */
    public function handle()
    {
        $delay = 0;
        foreach (UnityOrganization::withoutIssues()->get() as $organization) {
            CollectInsights::dispatch($organization, $this->option('since'), $this->option('until'))
                ->delay($delay)
                ->onQueue(AdsBoard::QUEUE_UNITY);

            // The rate limit is 1 request every 3 seconds per IP address.
            $delay += 3;
        }
    }
}
