<?php

namespace App\Console\Commands\Fixtures;

use App\Deposit;
use App\Lead;
use App\LeadOrderRoute;
use App\Offer;
use App\Result;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixExtraLeftoverOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:fix-extra-leftover-offers';

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
        $offers = Offer::where('name', 'like', 'LO\_LO\_%')->get();

        foreach ($offers as $offer) {
            $this->processOffer($offer);
        }

        return 0;
    }

    /**
     * Fixes shit with LO_LO_OFFER
     *
     * @param \App\Offer $offer
     *
     * @throws \Exception
     */
    protected function processOffer(Offer $offer)
    {
        /** @var \App\Offer $fixed */
        $fixed = Offer::firstWhere('name', Str::after($offer->name, 'LO_'));

        // Update leads
        Lead::whereOfferId($offer->id)->update(['offer_id' => $fixed->id]);

        // Update routes
        LeadOrderRoute::whereOfferId($offer->id)->each(function ($route) use ($fixed) {
            /** @var LeadOrderRoute $newRoute */
            $newRoute = $route->order->routes()->firstOrCreate([
                'offer_id'       => $fixed->id,
                'manager_id'     => $route->manager_id,
            ], [ 'leadsOrdered'  => $route->leadsOrdered,
                'leadsReceived'  => $route->leadsReceived,
            ]);

            $route->assignments()->update([
                'route_id' => $newRoute->id,
            ]);

            $route->delete();
        });

        // Update deposits
        Deposit::whereOfferId($offer->id)->update([
            'offer_id' => $fixed->id,
        ]);

        // Remove results.
        Result::whereOfferId($offer->id)->forceDelete();

        // Remove offer
        $offer->delete();
    }
}
