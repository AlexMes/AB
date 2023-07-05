<?php

namespace Tests\FRX;

use App\CRM\Tenant;
use App\LeadOrderAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CallbacksTest extends TestCase
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
    public function itRequiresAuthOnCreate()
    {
        Queue::fake();
        Event::fake();

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);

        $this->postJson(route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertStatus(401);
    }

    /** @test */
    public function callAtCannotBeNullOnCreate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_at' => null]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_at']);
    }

    /** @test */
    public function callAtCannotBeMissedOnCreate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->postJson(route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_at']);
    }

    /** @test */
    public function callAtMustBeDateOnCreate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_at' => Str::random()]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_at']);
    }

    /** @test */
    public function callAtCannotBeBeforeOrEqualOnCreate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_at' => now()->addMinutes(5)->toDateTimeString()]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_at']);
    }

    /** @test */
    public function callAtCanBeAfterOnCreate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_at' => now()->addMinutes(6)->toDateTimeString()]
        )
            ->assertJsonMissingValidationErrors(['call_at']);
    }



    /** @test */
    public function itCreatesCallbackOnCreate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'call_at' => now()->addMinutes(6)->toDateTimeString(),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'frx_lead_id' => Str::random(),
            'status'      => Str::random(),
        ]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->assertDatabaseCount('callbacks', 0);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(202);

        $this->assertDatabaseHas('callbacks', [
            'assignment_id' => $assignment->id,
            'phone'         => $assignment->lead->phone,
            'call_at'       => $newData['call_at'],
            'called_at'     => null,
            'frx_call_id'   => null,
        ]);
    }

    /** @test */
    public function itUpdatesCallbackIfItIsNotCalledYetOnCreate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'call_at' => now()->addHours(2)->toDateTimeString(),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'frx_lead_id' => Str::random(),
            'status'      => Str::random(),
        ]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $callback = $assignment->callbacks()->create([
            'phone'   => $assignment->lead->phone,
            'call_at' => Carbon::parse($newData['call_at'])->addHour()->toDateTimeString(),
        ]);

        $this->assertDatabaseCount('callbacks', 1);
        $this->assertNotEquals($newData['call_at'], $callback->call_at->toDateTimeString());

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(202);

        $this->assertDatabaseCount('callbacks', 1);
        $this->assertDatabaseHas('callbacks', [
            'assignment_id' => $assignment->id,
            'phone'         => $assignment->lead->phone,
            'call_at'       => $newData['call_at'],
            'called_at'     => null,
            'frx_call_id'   => null,
        ]);
    }

    /** @test */
    public function itCreatesAnotherCallbackIfPreviousIsCalledOnCreate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'call_at' => now()->addHours(2)->toDateTimeString(),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'frx_lead_id' => Str::random(),
            'status'      => Str::random(),
        ]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $callback = $assignment->callbacks()->create([
            'phone'     => $assignment->lead->phone,
            'call_at'   => Carbon::parse($newData['call_at'])->addHour()->toDateTimeString(),
            'called_at' => now()->toDateTimeString(),
        ]);

        $this->assertDatabaseCount('callbacks', 1);
        $this->assertNotEquals($newData['call_at'], $callback->call_at->toDateTimeString());

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(202);

        $this->assertDatabaseCount('callbacks', 2);
        $this->assertDatabaseHas('callbacks', [
            'assignment_id' => $assignment->id,
            'phone'         => $assignment->lead->phone,
            'call_at'       => $newData['call_at'],
            'called_at'     => null,
            'frx_call_id'   => null,
        ]);
    }

    /** @test */
    public function itReturns404IfLeadNotFoundOnCreate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'call_at' => now()->addMinutes(6)->toDateTimeString(),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => '100500']),
            $newData,
        )->assertStatus(404);
    }

    /** @test */
    public function itReturns404IfLeadDoesNotBelongToTheTenantOnCreate()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'call_at' => now()->addMinutes(6)->toDateTimeString(),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);

        $this->postJson(
            route('crm.frx.callbacks.store', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(404);
    }
}
