<?php


namespace App\Facebook\Jobs;

use App\Facebook\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StopCampaign implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Campaign
     */
    protected $campaign;

    /**
     * StopCampaign constructor.
     *
     * @param $campaign
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->campaign->stop();

            Log::info(sprintf('Campaign %s stopped', $this->campaign->id));
        } catch (\Throwable $exception) {
            Log::info(sprintf('Stopping campaign %s failed \n %s', $this->campaign->id, $exception->getMessage()));
        }
    }
}
