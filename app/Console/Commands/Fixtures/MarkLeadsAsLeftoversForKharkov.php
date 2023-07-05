<?php

namespace App\Console\Commands\Fixtures;

use App\CRM\Queries\ManagerAssignments;
use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Offer;
use Illuminate\Console\Command;

class MarkLeadsAsLeftoversForKharkov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:mark-leads-as-leftovers-for-kh2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var \App\LeadsOrder|\App\LeadsOrder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    protected $phones = [
        '79831087144',
        '79509669966',
        '79259331327',
        '79083023954',
        '89048597446',
        '79029585223',
        '89505521581',
        '89256870812',
        '79821680668',
        '79397820508',
        '79953617625',
        '89537901235',
        '89916367052',
        '89274385430',
        '89536313417',
        '89771376596',
        '89886939794',
        '79144670496',
        '89045910569',
        '89858451549',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->phones as $phone) {
            $id = ManagerAssignments::query()
                ->forOffice(5)
                ->forPeriod('2021-01-01 - 2021-03-31')
                ->search($phone)
                ->get()
                ->first();

            if ($id === null) {
                $this->info(sprintf("Phone number %s not found in assignments", $phone));
            } else {
                /** @var LeadOrderAssignment $assignment */
                $assignment = LeadOrderAssignment::with(['route','route.offer','lead'])->find($id['id']);

                $offer = Offer::firstOrCreate(['name' => 'LO_' . $assignment->route->offer->name]);

                $this->info(sprintf("Offer is %s. Phone is %s", $offer->name, $phone));

                $route = LeadOrderRoute::firstOrCreate([
                    'order_id'   => $assignment->route->order_id,
                    'offer_id'   => $offer->id,
                    'manager_id' => $assignment->route->manager_id,
                ]);

                $assignment->lead->update([
                    'offer_id' => $offer->id,
                ]);

                $assignment->update([
                    'route_id' => $route->id,
                ]);
            }
        }

        return 0;
    }
}
