<?php

namespace App\Console\Commands\Fixtures;

use App\CRM\Queries\ManagerAssignments;
use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Console\Command;

class MoveDoublesBetweenOffices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:move-doubles-between-offices';

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
        $assignments = ManagerAssignments::query()
            ->forPeriod('2021-01-26')
            ->forOffice(5)
            ->forOffer(264)
            ->havingStatus('Дубль')
            ->get();

        LeadOrderAssignment::whereIn('id', $assignments->pluck('id'))
            ->get()
            ->map(function (LeadOrderAssignment $assignment) {
                $og = $assignment->lead->events()
                    ->where('type', Lead::ASSIGNED)
                    ->orderBy('created_at')
                    ->first();

                return [
                    'assignment'     => $assignment->id,
                    'lead'           => $assignment->lead_id,
                    'first_assignee' => $og['custom_data']['manager_id']
                ];
            })->each(function ($fix) {
                $as = LeadOrderAssignment::find($fix['assignment']);

                $as->update([
                    'route_id' => LeadOrderRoute::where([
                        'order_id'   => '4329',
                        'offer_id'   => 264,
                        'manager_id' => $fix['first_assignee']
                    ])->value('id')
                ]);
            });

        return 0;
    }
}
