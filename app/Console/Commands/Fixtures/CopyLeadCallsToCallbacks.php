<?php

namespace App\Console\Commands\Fixtures;

use App\LeadOrderAssignment;
use Illuminate\Console\Command;

class CopyLeadCallsToCallbacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:copy-lead-calls-to-callbacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies lead calls data to callbacks.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::query()
            ->select(['id','lead_id','callback_at'])
            ->with(['lead:id,phone'])
            ->whereDoesntHave('callbacks')
            ->whereStatus('Перезвон')
            ->whereNotNull('callback_at')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->callbacks()->create([
                'phone'     => $assignment->lead->phone,
                'call_at'   => $assignment->callback_at->toDateTimeString(),
            ]);
        }

        return 0;
    }
}
