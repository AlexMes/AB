<?php

namespace App\Console\Commands\Reports;

use App\Facebook\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckBuyersForGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:buyer-manage-group
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
        $accounts    = Account::query()
            ->with(['user'])
            ->whereDate('created_at', '>=', $this->since->toDateString())
            ->whereDate('created_at', '<=', $this->until->toDateString())
            ->get();

        $rows = $accounts->groupBy('user.name')
            ->map(function ($group, $name) {
                return [
                    'user'          => $name,
                    'accounts'      => $group->count(),
                    'with_group'    => $group->whereNotNull('group_id')->count(),
                    'without_group' => $group->whereNull('group_id')->count(),
                ];
            });

        $this->table(['Баер','Колво аккаунтов','С группой','Без группы'], $rows);
    }
}
