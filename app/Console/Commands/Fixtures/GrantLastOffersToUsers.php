<?php

namespace App\Console\Commands\Fixtures;

use App\ManualAccount;
use App\ManualBundle;
use App\Offer;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class GrantLastOffersToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:grant-last-offers-to-users';

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
        $users = User::whereNotNull('branch_id')->whereNotNull('branch_id')->get(['id','name']);

        $offers = Offer::whereIn('id', ManualBundle::distinct()->whereHas('campaigns', function (Builder $campaignQuery) {
            return $campaignQuery->whereHas('insights', function ($insightQuery) {
                return $insightQuery->where('date', '>', '2020-01-01');
            });
        })->pluck('offer_id'))->pluck('id');


        /** @var \App\User $user */
        foreach ($users as $user) {
            $user->allowedOffers()->sync($offers);
        }

        return 0;
    }
}
