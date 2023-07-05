<?php

namespace App\Console\Commands\Reports;

use App\Lead;
use App\Office;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DuplicatesAcrossOffices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:duplicates-across-offices';

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
        ini_set("memory_limit", "-1");

        $offices = Office::query()
            ->with(['orders' => fn ($query) => $query->whereBetween('date', ['2020-09-01','2020-09-30'])->withReceived()])
            ->get()
            ->map(fn (Office $office) => [
                'name'   => $office->name,
                'leads'  => $office->orders->sum('received')
            ]);


        $assignments = Lead::query()
            ->whereHas('assignments')
            ->leftJoin('lead_order_assignments', 'lead_order_assignments.lead_id', '=', 'leads.id')
            ->leftJoin('lead_order_routes', 'lead_order_assignments.route_id', '=', 'lead_order_routes.id')
            ->leftJoin('leads_orders', 'lead_order_routes.order_id', '=', 'leads_orders.id')
            ->leftJoin('offers', 'leads.offer_id', '=', 'offers.id')
            ->leftJoin('offices', 'leads_orders.office_id', '=', 'offices.id')
            ->select([
                'leads.id',
                'leads.phone',
                DB::raw('offers.name as offerName'),
                DB::raw('offices.name as officeName'),
            ])->whereBetween('lead_order_assignments.created_at', ['2020-09-01 00:00:00','2020-09-30 23:59:59'])->get();

        $duplications =  $assignments->map(fn (Lead $lead) => [
            'phone'  => $lead->formatted_phone,
            'offer'  => $lead->offername,
            'office' => $lead->officename
        ])
            ->groupBy('phone')
            ->reject(fn ($group) => count($group) < 2);

        $cross =  $duplications->flatten(1)
            ->groupBy('office')
            ->map(fn ($group, $office) => ['office' => $office,'count' => count($group) ]);


//        dd($offices->first());

        $rows = $offices->map(fn ($office) => [
            'office' => $office['name'],
            'leads'  => $office['leads'],
            'cross'  => $cross[$office['name']]['count'] ?? 0,
            'rate'   => percentage($cross[$office['name']]['count'] ?? 0, $office['leads'])
        ]);


        $this->table(['office','leads','duplications','rate'], $rows);

        return 0;
    }
}
