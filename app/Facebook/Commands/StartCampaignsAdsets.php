<?php

namespace App\Facebook\Commands;

use App\Facebook\AdSet;
use App\Facebook\Jobs\StartAdset;
use Illuminate\Console\Command;

class StartCampaignsAdsets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb:start-campaigns-adsets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts selected adsets.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var AdSet $adset */
        $adsets = AdSet::where('start_midnight', true)->get();

        $adsets->each(function (AdSet $adSet) {
            StartAdset::dispatch($adSet);
        });

        AdSet::where('start_midnight', true)->update(['start_midnight' => false]);
    }
}
