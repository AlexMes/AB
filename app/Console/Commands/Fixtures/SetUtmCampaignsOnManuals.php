<?php

namespace App\Console\Commands\Fixtures;

use App\ManualCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetUtmCampaignsOnManuals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-utm-campaign-on-manual-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear from the signature, eh';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ManualCampaign::whereNull('utm_key')->each(function ($campaign) {
            return $campaign->update(['utm_key' => $this->resolveUtmKey($campaign)]);
        });

        return 0;
    }

    protected function resolveUtmKey(ManualCampaign $campaign)
    {
        if (Str::contains($campaign->name, 'campaign-')) {
            return trim(
                Str::afterLast($campaign->name, 'campaign-')
            );
        }

        return trim(
            Str::afterLast($campaign->name, '-')
        );
    }
}
