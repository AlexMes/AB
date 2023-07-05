<?php

namespace App\Console\Commands\Fixture;

use App\Offer;
use App\User;
use Illuminate\Console\Command;

class GrantLeftoversAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:fix-lo-access';

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
        User::query()->each(function (User $user) {
            $user->allowedOffers()->current()->each(function (Offer $offer) use ($user) {
                $loCopy = $offer->getLOCopy();

                if ($user->allowedOffers()->where('offers.id', $loCopy->id)->doesntExist()) {
                    $user->allowedOffers()->attach($loCopy);
                }
            });
        });

        return 0;
    }
}
