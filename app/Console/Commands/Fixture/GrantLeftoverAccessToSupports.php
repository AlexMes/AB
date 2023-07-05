<?php

namespace App\Console\Commands\Fixture;

use App\Offer;
use App\User;
use Illuminate\Console\Command;

class GrantLeftoverAccessToSupports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:grant-leftover-access-to-supports';

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
        $users = User::whereIn('role', [User::HEAD,User::SUPPORT,User::SUBSUPPORT])->get();

        $users->each(function (User $user) {
            $normalOffers = $user->allowedOffers()->whereNotIn('offers.id', Offer::leftovers()->pluck('id'));

            $normalOffers->each(function (Offer $offer) use ($user) {
                if ($user->allowedOffers()->where('offers.id', $offer->getLOCopy()->id)->doesntExist()) {
                    $user->allowedOffers()->attach($offer->getLOCopy());
                }
            });
        });

        return 0;
    }
}
