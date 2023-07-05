<?php

namespace App\Console\Commands\Fixtures;

use App\CRM\Queries\ManagerAssignments;
use App\LeadOrderAssignment;
use App\Manager;
use Illuminate\Console\Command;

class SplitNewLeadsForKiev1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:split-new-leads-for-kiev-1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $managers  = [689,812,801,1027,1063,1074,815,333,1040,1024,1094,1109];
        $iteration = 0;

        $assignments = ManagerAssignments::query()->forOffice(16)
            ->forPeriod('2020-10-01 to 2020-11-22')
            ->havingStatus('Новый')
            ->get();

        $assignments->chunk(5)
            ->each(
                function ($chunk) use ($managers, &$iteration) {
                    $this->info(sprintf("manager %d %d", $iteration, $managers[$iteration]));
                    LeadOrderAssignment::whereIn('id', $chunk->pluck('id'))
                        ->each(fn ($assignment) => $assignment->transfer(Manager::find($managers[$iteration])));
                    $iteration++;
                }
            );

        return 0;
    }
}
