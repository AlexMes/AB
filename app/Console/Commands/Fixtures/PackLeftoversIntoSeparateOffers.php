<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PackLeftoversIntoSeparateOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:pack-leftovers-into-separate-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do not really need desc';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $offers = Offer::whereIn(
            'id',
            $this->queryLeftovers()->distinct()->pluck('offer_id')
        )->get();

        // $offers = Offer::whereIn('id', [3])->get();
        foreach ($offers as $offer) {
            $this->createOfferAndMigrateLeadsForOffer($offer);
        }

        return 0;
    }

    /**
     *
     * @return \App\Lead|\Illuminate\Database\Eloquent\Builder
     */
    protected function queryLeftovers()
    {
        return Lead::leftovers(['2021-10-01','2021-10-03']);
    }

    /**
     * @param $offer
     */
    protected function createOfferAndMigrateLeadsForOffer($offer): void
    {
        if (Str::startsWith($offer->name, 'LO_')) {
            return;
        }

        $this->queryLeftovers()->where('offer_id', $offer->id)->update([
            'offer_id' => Offer::firstOrCreate([
                'name' => sprintf("LO_%s", $offer->name),
            ])->id,
        ]);
    }
}
