<?php

namespace App\Console\Commands\Fixtures;

use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;

class CopyLeadLabelsToAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:copy-lead-labels-to-assignment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies lead labels to its assignment.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assignments = LeadOrderAssignment::query()
            ->with(['lead.labels'])
            ->whereHas('lead.labels')
            ->orderBy('id');
        $this->getOutput()->progressStart($assignments->count());

        /** @var LeadOrderAssignment $assignment */
        foreach ($assignments->cursor() as $assignment) {
            $assignment->labels()->sync($assignment->lead->labels->pluck('id'));

            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();

        return 0;
    }
}
