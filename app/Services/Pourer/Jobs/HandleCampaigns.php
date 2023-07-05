<?php

namespace App\Services\Pourer\Jobs;

use App\Facebook\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class HandleCampaigns implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $campaigns;

    /**
     * Number of attempts
     *
     * @var int
     */
    public $tries = 2;

    /**
     * CollectAccounts constructor.
     *
     * @param array $campaigns
     */
    public function __construct(array $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->process();
    }

    /**
     * Cache campaigns
     *
     * @return void
     */
    public function process()
    {
        collect($this->campaigns)->each(fn (array $campaign) => $this->save($campaign));
    }

    /**
     * Save retrieved campaign into database
     *
     * @param array $campaign
     *
     * @return void
     */
    protected function save(array $campaign)
    {
        Campaign::updateOrCreate(['id' => $campaign['id']], Arr::only($campaign, Campaign::FB_FIELDS));
    }

    /**
     * @return \Carbon\Carbon
     */
    public function retryUntil()
    {
        return now()->addSeconds(60);
    }
}
