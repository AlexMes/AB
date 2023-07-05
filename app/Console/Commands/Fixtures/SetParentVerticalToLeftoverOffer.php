<?php

namespace App\Console\Commands\Fixtures;

use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetParentVerticalToLeftoverOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:set-lo-offer-vertical';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set vertical to LO offers from their parents';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var Offer $offer */
        foreach (Offer::leftovers()->get() as $offer) {
            $parentOffer = Offer::firstWhere('name', Str::after($offer->name, 'LO_'));

            if ($parentOffer !== null) {
                $offer->update([
                    'vertical' => $parentOffer->vertical,
                ]);
            }
        }

        return 0;
    }
}
