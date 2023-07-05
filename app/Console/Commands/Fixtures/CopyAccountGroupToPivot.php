<?php

namespace App\Console\Commands\Fixtures;

use App\ManualAccount;
use Illuminate\Console\Command;

class CopyAccountGroupToPivot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:copy-manual-account-group-to-pivot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies manual account groups to pivot table.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accounts = ManualAccount::whereNotNull('group_id');
        $this->getOutput()->progressStart($accounts->count());

        /** @var ManualAccount $account */
        foreach ($accounts->cursor() as $account) {
            $account->groups()->syncWithoutDetaching([$account->group_id]);

            $this->getOutput()->progressAdvance(1);
        }

        $this->getOutput()->progressFinish();

        return 0;
    }
}
