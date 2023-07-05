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

class MarkCallbackCalledTest extends TestCase
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
    public function itRequiresAuth()
    {
        Queue::fake();
        Event::fake();

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);

        $this->postJson(route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertStatus(401);
    }

    /** @test */
    public function calledAtCannotBeNull()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['called_at' => null]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['called_at']);
    }

    /** @test */
    public function calledAtCanBeMissed()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertJsonMissingValidationErrors(['called_at']);
    }

    /** @test */
    public function calledAtMustBeDate()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['called_at' => Str::random()]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['called_at']);
    }

    /** @test */
    public function calledAtCannotBeAfter()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['called_at' => now()->addMinutes(1)->toDateTimeString()]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['called_at']);
    }

    /** @test */
    public function calledAtCanBeBeforeOrEqual()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['called_at' => now()->subMinutes(1)->toDateTimeString()]
        )
            ->assertJsonMissingValidationErrors(['called_at']);
    }

    /** @test */
    public function callIdCannotBeNull()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_id' => null]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_id']);
    }

    /** @test */
    public function callIdCanBeMissed()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]))
            ->assertJsonMissingValidationErrors(['call_id']);
    }

    /** @test */
    public function callIdCannotBeInteger()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_id' => 123]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_id']);
    }

    /** @test */
    public function callIdCannotBeLongerInteger()
    {
        Queue::fake();
        Event::fake();
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
        ]);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            ['call_id' => Str::random(256)],
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors(['call_id']);
    }



    /** @test */
    public function itDoesNotCreateCallbackIfNoActualOneFound()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'called_at' => now()->subMinutes(1)->toDateTimeString(),
            'call_id'   => Str::random(255),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
            'call_at'     => now()->addHours(2)->toDateTimeString(),
            'called_at'   => now()->toDateTimeString(),
            'frx_call_id' => Str::random(32),
        ]);

        $this->assertDatabaseCount('callbacks', 1);

        $response = $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData
        )->assertStatus(404);

        $this->assertTrue(Str::startsWith($response->exception->getMessage(), 'No actual callback found'));
        $this->assertDatabaseCount('callbacks', 1);
    }

    /** @test */
    public function itDoesNotUpdateCallbackIfNoActualOneFound()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'called_at' => now()->subMinutes(1)->toDateTimeString(),
            'call_id'   => Str::random(255),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $callback = $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
            'call_at'     => now()->addHours(2)->toDateTimeString(),
            'called_at'   => now()->toDateTimeString(),
            'frx_call_id' => Str::random(32),
        ]);
        $this->assertNotEquals($callback->called_at->toDateTimeString(), $newData['called_at']);
        $this->assertNotEquals($callback->frx_call_id, $newData['call_id']);

        $response = $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData
        )->assertStatus(404);

        $this->assertTrue(Str::startsWith($response->exception->getMessage(), 'No actual callback found'));
        $this->assertNotEquals($callback->refresh()->called_at->toDateTimeString(), $newData['called_at']);
        $this->assertNotEquals($callback->frx_call_id, $newData['call_id']);
    }

    /** @test */
    public function itUpdatesCallbackIfActualOneFound()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'called_at' => now()->subMinutes(1)->toDateTimeString(),
            'call_id'   => Str::random(255),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $callback = $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
            'call_at'     => now()->addHours(2)->toDateTimeString(),
        ]);
        $this->assertNotEquals(optional($callback->called_at)->toDateTimeString(), $newData['called_at']);
        $this->assertNotEquals($callback->frx_call_id, $newData['call_id']);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData
        )->assertStatus(202);

        $this->assertEquals($newData['called_at'], $callback->refresh()->called_at->toDateTimeString());
        $this->assertEquals($newData['call_id'], $callback->frx_call_id);
    }

    /** @test */
    public function itUpdatesCalledAtAsNowIfNoParamProvided()
    {
        Queue::fake();
        Event::fake();
        Carbon::setTestNow(now()->hour(9)->minute(0)->seconds(0));

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'call_id'   => Str::random(255),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $callback = $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
            'call_at'     => now()->addHours(2)->toDateTimeString(),
        ]);
        $this->assertNull($callback->called_at);

        $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData
        )->assertStatus(202);

        $this->assertNotNull($callback->refresh()->called_at);
        $this->assertEquals(now()->toDateTimeString(), $callback->called_at->toDateTimeString());
    }

    /** @test */
    public function itReturns404IfLeadNotFound()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'called_at' => now()->subMinutes(1)->toDateTimeString(),
            'call_id'   => Str::random(255),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->route->manager->update(['frx_tenant_id' => $tenant->id]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
            'call_at'     => now()->addHours(2)->toDateTimeString(),
        ]);

        $response = $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => '100500']),
            $newData,
        )->assertStatus(404);

        $this->assertFalse(Str::startsWith($response->exception->getMessage(), 'No actual callback found'));
    }

    /** @test */
    public function itReturns404IfLeadDoesNotBelongToTheTenant()
    {
        Queue::fake();
        Event::fake();

        /** @var Tenant $tenant */
        $tenant = factory(Tenant::class)->create();
        $this->signIn($tenant);

        $newData = [
            'called_at' => now()->subMinutes(1)->toDateTimeString(),
            'call_id'   => Str::random(255),
        ];
        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['frx_lead_id' => Str::random()]);
        $assignment->callbacks()->create([
            'phone'       => $assignment->lead->phone,
            'call_at'     => now()->addHours(2)->toDateTimeString(),
        ]);

        $response = $this->postJson(
            route('crm.frx.callbacks.mark-called', ['frx_assignment' => $assignment->frx_lead_id]),
            $newData,
        )->assertStatus(404);

        $this->assertFalse(Str::startsWith($response->exception->getMessage(), 'No actual callback found'));
    }
}
