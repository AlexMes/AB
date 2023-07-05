<?php

namespace App\Console\Commands;

use App\AdsBoard;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;

class ReplaceAssignmentLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:replace-lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replaces assignment lead if delivery failed & has replace marker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::query()
            ->with(['route.order'])
            ->whereNull('confirmed_at')
            ->whereNotNull('delivery_failed')
            ->whereNotNull('replace_auth_id');

        $assignments->each(function (LeadOrderAssignment $assignment) {
            \App\Jobs\ReplaceAssignmentLead::dispatch($assignment)->onQueue(AdsBoard::QUEUE_DEFAULT);
        });

        return 0;
    }
}
