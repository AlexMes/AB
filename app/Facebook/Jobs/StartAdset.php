<?php

namespace App\Facebook\Jobs;

use App\Facebook\AdSet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartAdset implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var AdSet
     */
    public AdSet $adset;

    /**
     * Create a new job instance.
     *
     * @param \App\Facebook\AdSet $adset
     */
    public function __construct(AdSet $adset)
    {
        $this->adset = $adset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->adset->start();
    }
}
