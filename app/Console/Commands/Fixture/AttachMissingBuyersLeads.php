<?php

namespace App\Console\Commands\Fixture;

use App\AdsBoard;
use App\Lead;
use App\Offer;
use App\User;
use Illuminate\Console\Command;

class AttachMissingBuyersLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:attach-missing-buyers-leads';

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
        $users = User::select(['id', 'name', 'binomTag'])->whereNotNull('binomTag')->orderByRaw('length(users."binomTag")')->get();

        $report = 'Fixed buyers on leads.'.PHP_EOL;

        $users->chunk(10)->each(fn ($chunk) => $chunk->each(function (User $user) use (&$report) {
            $fixed = Lead::query()
                ->whereNull('user_id')
                ->whereNull('affiliate_id')
                ->where('utm_source', 'ilike', '%'.$user->binomTag.'%')
                ->update(['user_id' => $user->id]);

            $report .= $user->name;
            $report .= ' Updated '.$fixed.' leads for '.PHP_EOL;
        }));

        AdsBoard::report($report);

        $base = Lead::whereNull('user_id')
            ->whereNull('affiliate_id')
            ->whereNotNull('offer_id')
            ->whereBetween('created_at', [now()->subMonth(), now()->subMinutes(5)]);

        $offers = $base->distinct()->pluck('offer_id');

        foreach ($offers as $offer) {
            $branches = Lead::where('offer_id', $offer)
                ->join('users', 'users.id', '=', 'leads.user_id')
                ->join('branches', 'branches.id', '=', 'users.branch_id')
                ->whereBetween('leads.created_at', [now()->subMonth(), now()->subMinutes(5)])
                ->distinct()
                ->pluck('branches.id')
                ->values();

            if ($branches->count() > 1) {
                AdsBoard::report('Branch conflict for offer '.$offer.' review manually.');
            }


            if ($branches->count() === 1) {
                $branch = $branches->first();
                // shaman
                if ($branch === 16) {
                    $leads = $base->where('offer_id', $offer)->update(['user_id' => 237]);
                    AdsBoard::report('Updated '.$leads .' for shaman'.Offer::find($offer)->name);
                }

                // jordan
                if ($branch === 19) {
                    $leads = $base->where('offer_id', $offer)->update(['user_id' => 233]);
                    AdsBoard::report('Updated '.$leads .' for jordan '.Offer::find($offer)->name);
                }

                // x
                if ($branch === 20) {
                    $leads = $base->where('offer_id', $offer)->update(['user_id' => 236]);
                    AdsBoard::report('Updated '.$leads .' for x '.Offer::find($offer)->name);
                }
            }
        }


        return 0;
    }
}
