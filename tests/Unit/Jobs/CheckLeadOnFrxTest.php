<?php

namespace Tests\Unit\Jobs;

use App\CRM\Tenant;
use App\Jobs\CheckLeadOnFrx;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckLeadOnFrxTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRecordsFrxLeadId()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $tenant  = factory(Tenant::class)->create();
        $manager = factory(Manager::class)->create([
            'office_id'        => $office->id,
            'frx_access_token' => Str::random(),
            'frx_tenant_id'    => $tenant->id,
        ]);

        $this->setGuard('crm')->signIn($manager);

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $leadId = Str::random();
        Http::fake([
            '*' => Http::response([
                'id' => $leadId,
            ]),
        ]);

        CheckLeadOnFrx::dispatchNow($assignment);

        $this->assertEquals($leadId, $assignment->refresh()->frx_lead_id);
    }
}
