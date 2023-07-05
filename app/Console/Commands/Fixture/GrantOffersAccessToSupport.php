<?php

namespace App\Console\Commands\Fixture;

use App\User;
use Illuminate\Console\Command;

class GrantOffersAccessToSupport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:grant-offers-access-to-support';

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
        $users    = User::where('role', User::SUPPORT)->get();

        $users->each(function (User $user) {
            $offers = \App\Offer::whereHas('leads', function ($leadQuery) use ($user) {
                return $leadQuery->where('leads.created_at', '>', '2021-11-10')->joinSub(User::whereBranchId($user->branch_id), 'users', 'leads.user_id', '=', 'users.id');
            })->get();

            $user->allowedOffers()->sync($offers);
        });

        return 0;
    }
}
