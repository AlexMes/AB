<?php

namespace Leads\Pipes;

use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Leads\Pipes\EnsureLeadIsNotAssigned;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EnsureLeadIsNotAssignedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /** @test */
    public function itPassLeadWhenNoAssignmentsFound()
    {
        $lead = factory(Lead::class)->create();

        $pipe = new EnsureLeadIsNotAssigned();

        $pipe->handle($lead, fn ($lead) => $this->assertNotEmpty($lead, 'Lead does not pass'));
    }

    /** @test */
    public function itDoesntPassLeadIfItWasAssignedLessThanMonthBefore()
    {
        $lead = factory(Lead::class)->create();

        LeadOrderAssignment::create([
            'lead_id'    => $lead->id,
            'route_id'   => factory(LeadOrderRoute::class)->create()->id,
            'created_at' => now()->toDateTimeString(),
        ]);

        $pipe = new EnsureLeadIsNotAssigned();
        $this->assertNotEmpty($lead->assignments);

        $pipe->handle($lead, fn ($lead) => $this->assertEmpty($lead, 'Lead did pass'));
    }

    /** @test */
    public function itDoesPassLeadIfItWasAssignedMoreThanMonthBefore()
    {
        $lead = factory(Lead::class)->create();

        LeadOrderAssignment::create([
            'lead_id'    => $lead->id,
            'route_id'   => factory(LeadOrderRoute::class)->create()->id,
            'created_at' => now()->subMonth()->toDateTimeString(),
        ]);

        $pipe = new EnsureLeadIsNotAssigned();
        $this->assertNotEmpty($lead->assignments);

        $pipe->handle($lead, fn ($lead) => $this->assertNotEmpty($lead, 'Lead did not pass'));
    }
}
