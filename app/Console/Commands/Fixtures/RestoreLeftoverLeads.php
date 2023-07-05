<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RestoreLeftoverLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:restore-leftover-leads';

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
        $offers = Offer::leftovers()->get(['id','name']);

        foreach ($offers as $offer) {
            $this->migrateLeadsFromLeftoversForOffer($offer);
        }

        return 0;
    }

    protected function queryLeftovers()
    {
        return Lead::leftovers(['2020-12-19', '2020-12-20']);
    }

    /**
     * Move leads back to the original offer
     *
     * @param $offer
     */
    protected function migrateLeadsFromLeftoversForOffer($offer)
    {
        if (in_array($offer->id, [])) {
        }
        $this->queryLeftovers()->where('offer_id', $offer->id)->update([
            'offer_id' => Offer::firstWhere([
                'name' => Str::after($offer->name, 'LO_'),
            ])->id,
        ]);
    }
}
