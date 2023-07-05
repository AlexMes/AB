<?php

namespace App\Console\Commands\Fixture;

use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetOfferUUID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-offer-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets offer uuid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Offer::whereNull('uuid')
            ->orWhere('uuid', '')
            ->each(fn (Offer $offer) => $offer->update(['uuid' => Str::uuid()->toString()]));

        return 0;
    }
}
