<?php

namespace App\Console\Commands;

use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CalculateAssignmentBenefit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:calc-benefit
                            {since? : Assignment since date}
                            {until? : Assignment until date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate assignment benefit rely on payment conditions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $since = Carbon::parse($this->argument('since'))->startOfDay();
        $until = Carbon::parse($this->argument('until'))->endOfDay();
        if ($since->greaterThan($until)) {
            $until = $since->copy();
        }

        $assignments = LeadOrderAssignment::payable()
            ->with(['route.order'])
            ->whereBetween('created_at', [$since, $until]);

        $progress = $this->output->createProgressBar($assignments->count());

        /** @var LeadOrderAssignment $assignment */
        foreach ($assignments->cursor() as $assignment) {
            $assignment->updateBenefit();

            $progress->advance();
        }

        $progress->finish();

        return 0;
    }
}
