<?php

namespace App\Console\Commands;

use App\AssignmentDayToDaySnapshot;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;

class MakeAssignmentDTDSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:make-dtd-snapshot';

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
        $date   = now()->subDay()->toImmutable();
        $result = LeadOrderAssignment::query()
            ->selectRaw(
                "
                managers.id as manager_id,
                managers.name as manager_name,
                offers.id as offer_id,
                offers.name as offer_name,
                count(lead_order_assignments.id) as total,
                count(CASE WHEN lead_order_assignments.status = 'Депозит' THEN 1 END) AS deposits,
                count(CASE WHEN lead_order_assignments.status = 'Нет ответа' THEN 1 END) AS no_answer
                "
            )
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', '=', 'lead_order_routes.id')
            ->leftJoin('offers', 'lead_order_routes.offer_id', '=', 'offers.id')
            ->leftJoin('managers', 'lead_order_routes.manager_id', 'managers.id')
            ->whereBetween('lead_order_assignments.created_at', [$date->startOfDay(), $date->endOfDay()])
            ->groupBy(['managers.id', 'offers.id'])
            ->get();

        foreach ($result as $row) {
            AssignmentDayToDaySnapshot::firstOrCreate(
                [
                    'date'       => $date->toDateString(),
                    'manager_id' => $row->manager_id,
                    'offer_id'   => $row->offer_id,
                ],
                [
                    'manager'    => $row->manager_name,
                    'offer'      => $row->offer_name,
                    'total'      => $row->total,
                    'deposits'   => $row->deposits,
                    'no_answer'  => $row->no_answer,
                ]
            );
        }

        return 0;
    }
}
