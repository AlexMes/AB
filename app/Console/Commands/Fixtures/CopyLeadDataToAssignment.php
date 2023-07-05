<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;

class CopyLeadDataToAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:copy-lead-data-to-assignment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies lead data to its assignment.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::query()
            ->with(['lead'])
            ->orderBy('id');
        $this->getOutput()->progressStart($assignments->count());

        /** @var LeadOrderAssignment $assignment */
        foreach ($assignments->cursor() as $assignment) {
            $assignment->timestamps    = false;
            $assignment->gender_id     = $assignment->lead->gender_id;
            $assignment->external_id   = $assignment->lead->external_id;
            $assignment->called_at     = $assignment->lead->called_at;
            $assignment->timezone      = $assignment->lead->timezone;
            $assignment->age           = $assignment->lead->age;
            $assignment->profession    = $assignment->lead->profession;
            $assignment->reject_reason = $assignment->lead->reject_reason;
            $assignment->status        = $assignment->lead->status;
            $assignment->comment       = $assignment->lead->comment;
            $assignment->deposit_sum   = $assignment->lead->deposit_sum;
            $assignment->alt_phone     = $assignment->lead->alt_phone;
            $assignment->benefit       = $assignment->lead->benefit;
            $assignment->callback_at   = $assignment->lead->callback_at;
            $assignment->frx_call_id   = $assignment->lead->frx_call_id;
            $assignment->frx_lead_id   = $assignment->lead->frx_lead_id;
            $assignment->registered_at = $assignment->lead->created_at;

            $assignment->saveQuietly();

            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();

        return 0;
    }
}
