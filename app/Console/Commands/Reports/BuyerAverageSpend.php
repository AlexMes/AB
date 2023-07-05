<?php

namespace App\Console\Commands\Reports;

use App\Facebook\Account;
use App\Insights;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class BuyerAverageSpend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:buyer-average-spend
                            {--range : Range for the report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get average spend by buyer';

    /**
     * @var \Carbon\Carbon
     */
    protected $until;

    /**
     * @var \Carbon\Carbon
     */
    protected $since;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->since = Carbon::parse($this->ask('Since date'))->startOfDay();
        $this->until = Carbon::parse($this->ask('Until date'))->endOfDay();
        $accounts    =  Account::query()
            ->with(['profile','profile.user'])
            ->whereBetween('created_at', [
                $this->since,$this->until
            ])->get();


        $rows = $accounts->unique('account_id')
            ->groupBy('profile.user_id')
            ->map(function (Collection $group) {
                return [
                    'user'       => $group->first()->profile->user->name,
                    'accounts'   => $group->count(),
                    'totalSpend' => Insights::whereIn('account_id', $group->pluck('account_id'))
                        ->whereBetween('created_at', [
                            $this->since,$this->until
                        ])->get(['spend'])->sum('spend'),
                    'avgSpend' => round(Insights::whereIn('account_id', $group->pluck('account_id'))
                        ->whereBetween('created_at', [
                            $this->since,$this->until
                        ])->get(['spend'])->sum('spend') / $group->count(), 2)
                ];
            });

        $this->table(['buyer','acc','spend','avg.spend'], $rows);
    }
}
