<?php

namespace App\Console\Commands\Fixture;

use App\Lead;
use App\ManualAccount;
use App\ManualBundle;
use App\User;
use Illuminate\Console\Command;

class CleanupAllowedOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:cleanup-allowed-offers';

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
        $users = User::whereIn('role', [User::BUYER, User::TEAMLEAD, User::HEAD])->get();

        $users = $users->sortBy(function ($user) {
            switch ($user->role) {
                case User::HEAD:
                    return 3;
                case User::TEAMLEAD:
                    return 2;
                default:
                    return 1;
            }
        });

        foreach ($users as $user) {
            $this->cleanupOffersForUser($user);
        }

        return 0;
    }

    /**
     * @param \App\User $user
     */
    protected function cleanupOffersForUser(User $user)
    {
        $user->allowedOffers()->sync($this->getOffers($user));
    }

    /**
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getOffers(User $user)
    {
        if ($user->isBuyer()) {
            return $this->getLatestOffers([$user->id])->merge($this->getTrafficOffers([$user->id]));
        }

        if ($user->isTeamLead()) {
            $sub = $user->teams->flatMap->users->pluck('id');

            return $this->getLatestOffers($sub)->merge($this->getTrafficOffers($sub));
        }

        if ($user->isBranchHead() && $user->branch_id !== null) {
            $sub = $user->branch->users()->pluck('id')->toArray();

            return $this->getLatestOffers($sub)->merge($this->getTrafficOffers($sub));
        }
    }

    /**
     * Get latest offers from current month
     *
     * @param array $users
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getLatestOffers($users)
    {
        return Lead::whereIn('user_id', $users)->whereBetween('created_at', [
            now()->subMonths(3),
            now()
        ])->distinct()->pluck('offer_id');
    }

    /**
     * @param $users
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTrafficOffers($users)
    {
        return ManualBundle::whereHas('insights', function ($q) use ($users) {
            return $q->whereBetween('manual_insights.date', [
                now()->startOfMonth()->toDateString(),
                now()->toDateString(),
            ])->whereIn('manual_insights.account_id', ManualAccount::whereIn('user_id', $users)->pluck('account_id'));
        })->distinct()->pluck('offer_id') ;
    }
}
