<?php

namespace App\Console\Commands;

use App\ResellBatch;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StartBranchBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branch-batch:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigning batches from branch 19 for start';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ResellBatch::query()
            ->whereNull('assign_until')
            ->where('branch_id', 19)
            ->latest('created_at')
            ->first()
            ->update([
                'status'        => ResellBatch::IN_PROCESS,
                'assign_until'  => Carbon::now()->startOfDay()->addHours(15),
            ]);

        return 0;
    }
}
