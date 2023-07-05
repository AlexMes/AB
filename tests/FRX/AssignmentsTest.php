<?php

namespace Tests\FRX;

use App\CRM\Status;
use App\CRM\Tenant;
use App\LeadOrderAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssignmentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepare test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setGuard('tenant');
    }

    /** @test */
    public function itRequiresAuthOnUpdate()
    {
        Queue::fake();
        Event::fake();

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);

        $this->putJson(route('crm.frx.assignments.update', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertStatus(401);
    }

    /** @test */
    public function statusCannotBeNullOnUpdate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->putJson(route('crm.frx.assignments.update', ['frx_assignment' => $assignment->frx_lead_id]), ['status' => null])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function statusCanBeMissedOnUpdate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->putJson(route('crm.frx.assignments.update', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertJsonMissingValidationErrors(['status']);
    }

    /** @test */
    public function statusCannotBeAnyStringOnUpdate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->putJson(
            route('crm.frx.assignments.update', ['frx_assignment' => $assignment->frx_lead_id]),
            ['status' => Str::random()],
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function itSavesLeadOnUpdate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'status' => Status::first()->name,
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'frx_lead_id' => Str::random(),
            'status'      => Str::random(),
        ]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->assertNotEquals($newData['status'], $assignment->status);

        $this->putJson(
            route('crm.frx.assignments.update', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(202);

        $this->assertDatabaseHas('lead_order_assignments', array_merge($assignment->getAttributes(), $newData));
    }

    /** @test */
    public function itReturns404IfLeadNotFoundOnUpdate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'status' => Status::first()->name,
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->assertNotEquals($newData['status'], $assignment->status);

        $this->putJson(
            route('crm.frx.assignments.update', ['frx_assignment' => '100500']),
            $newData,
        )->assertStatus(404);
    }

    /** @test */
    public function itReturns404IfLeadDoesNotBelongToTheTenantOnUpdate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'status' => Status::first()->name,
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);

        $this->assertNotEquals($newData['status'], $assignment->status);

        $this->putJson(
            route('crm.frx.assignments.update', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(404);
    }
}
