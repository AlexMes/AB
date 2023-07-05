<?php

namespace App\Console\Commands;

use App\Jobs\CreateBatchWithLeads;
use App\LeadDestination;
use App\LeadOrderAssignment;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CollectLeadsForBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:leads-batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collecting leads of 19 branch and create resell batch of these leads';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(2);

        $leads = LeadOrderAssignment::whereHas('lead.user', fn ($query) => $query->where('branch_id', 19))
            ->whereBetween('created_at', [$date, Carbon::now()])
            ->whereIn('status', [
                'Na zameny', 'Dubl', 'Дубликат', 'Дубль', 'Return'
            ])
            ->whereNotExists(function (Builder $builder) {
                return $builder->select(DB::raw(1))
                    ->from('lead_resell_batch')
                    ->whereColumn('lead_resell_batch.assignment_id', 'lead_order_assignments.id')
                    ->whereDate('assigned_at', '>=', now()->subDays(3)->toDateString());
            })
            ->pluck('lead_id')
            ->toArray();

        $offices = LeadOrderAssignment::whereIn('lead_id', $leads)
            ->leftJoin('lead_order_routes', function ($join) {
                $join->on('lead_order_assignments.route_id', '=', 'lead_order_routes.id')
                    ->whereIn(
                        'lead_order_routes.destination_id',
                        LeadDestination::where('branch_id', 19)->pluck('id')
                    );
            })
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', 'leads_orders.id')
            ->join('offices', 'leads_orders.office_id', 'offices.id')
            ->select('offices.id')
            ->where('offices.id', '!=', 157)
            ->pluck('id')
            ->toArray();

        CreateBatchWithLeads::dispatch($leads, $offices);

        return 0;
    }
}
