<?php

namespace App\Console\Commands;

use App\LeadOrderAssignment;
use Illuminate\Console\Command;

class ResetConfirmedOnAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:reset-confirmed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets confirmed_at to null on assignments with double status on 16 branch.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LeadOrderAssignment::query()
            ->whereHas('route.order', fn ($query) => $query->where('branch_id', 16))
            ->whereDate('created_at', '>=', now()->startOfMonth()->toDateString())
            ->whereIn('status', ['Дубль'])
            ->whereNotNull('confirmed_at')
            ->update(['confirmed_at' => null]);

        return 0;
    }
}
