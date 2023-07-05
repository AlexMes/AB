<?php

namespace App\Console\Commands\Fixture;

use App\ManualCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateUtmKeysForCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:update-utm-keys-for-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ManualCampaign::where('name', 'ilike', '%utm_campaign%')->each(function (ManualCampaign $manualCampaign) {
            $manualCampaign->update([
                'name' => Str::before($manualCampaign->name, '&utm_campaign'),
            ]);
        });

        return 0;
    }
}
