<?php

namespace App\Console\Commands;

use App\Affiliate;
use App\Deposit;
use App\Lead;
use App\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SetUserToLeadsAndDeps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-lead-dep-user
                            {--user_id= : user id to set}
                            {--branch_id= : branch id where to set}';

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
        $userId   = (int)$this->option('user_id');
        $branchId = (int)$this->option('branch_id');
        $since    = Carbon::parse('2022-08-01 00:00:00');

        if (empty($userId) || empty($branchId)) {
            $this->info("User or branch is not specified.");

            return 0;
        }

        $offers = Offer::whereBranchId($branchId)->pluck('id');

        Lead::whereNull('user_id')
            ->where('created_at', '>', $since->toDateTimeString())
            ->whereIn('affiliate_id', Affiliate::whereBranchId($branchId)->pluck('id'))
            ->update(['user_id' => $userId]);

        Lead::whereNull('user_id')
            ->where('created_at', '>', $since->toDateTimeString())
            ->whereIn('offer_id', $offers)
            ->update(['user_id' => $userId]);

        Deposit::whereNull('user_id')
            ->where('created_at', '>', $since->toDateTimeString())
            ->whereIn('offer_id', $offers)
            ->update(['user_id' => $userId]);

        return 0;
    }
}
