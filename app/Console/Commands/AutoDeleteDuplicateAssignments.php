<?php

namespace App\Console\Commands;

use App\LeadsOrder;
use Illuminate\Console\Command;

class AutoDeleteDuplicateAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:delete-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete order assignments where status is duplicate."';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LeadsOrder::current()
            ->whereAutodeleteDuplicates(true)
            ->with(['assignments' => function ($query) {
                return $query->whereIn('lead_order_assignments.status', ['Дубль']);
            }])
            ->get()
            ->flatMap
            ->assignments
            ->each
            ->remove();

        return 0;
    }
}
