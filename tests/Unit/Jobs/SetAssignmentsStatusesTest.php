<?php

namespace Tests\Unit\Jobs;

use App\Events\Lead\Created;
use App\Events\LeadAssigned;
use App\Jobs\SetAssignmentsStatuses;
use App\Lead;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\StatusConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PresetLeadAssignments;
use Tests\TestCase;

class SetAssignmentsStatusesTest extends TestCase
{
    use RefreshDatabase;

    protected LeadOrderAssignment $assignment;
    protected string $newStatus;
    protected string $oldStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PresetLeadAssignments::class]);
        Event::fake([Created::class, LeadAssigned::class]);

        $this->newStatus  = 'new_status';
        $this->oldStatus  = 'old_status';
        $lead             = factory(Lead::class)->create();
        $this->assignment = factory(LeadOrderAssignment::class)->create([
            'lead_id'    => $lead->id,
            'route_id'   => LeadOrderRoute::first()->id,
            'status'     => $this->oldStatus,
            'created_at' => now()->subDays(2),
        ]);
    }

    /** @test */
    public function itRecordsEvent()
    {
        $statusConfig = factory(StatusConfig::class)->state('in')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => [$this->oldStatus],
            'assigned_days_ago' => 2,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $this->assignment->id,
            'eventable_type' => $this->assignment->events()->getMorphClass(),
            'type'           => 'updated',
            'changed_data'   => json_encode(['status' => $statusConfig->new_status]),
        ]);
    }

    /** @test */
    public function itUpdatesStatusIfTypeIsIn()
    {
        $statusConfig = factory(StatusConfig::class)->state('in')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => [$this->oldStatus],
            'assigned_days_ago' => 2,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->newStatus, $this->assignment->refresh()->status);
    }

    /** @test */
    public function itUpdatesStatusIfTypeIsOut()
    {
        $statusConfig = factory(StatusConfig::class)->state('out')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => ['another_status'],
            'assigned_days_ago' => 2,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->newStatus, $this->assignment->refresh()->status);
    }

    /** @test */
    public function itDoesNotUpdateStatusIfAnotherOffice()
    {
        $statusConfig = factory(StatusConfig::class)->state('in')->create([
            'new_status'        => $this->newStatus,
            'statuses'          => [$this->oldStatus],
            'assigned_days_ago' => 2,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->oldStatus, $this->assignment->refresh()->status);
    }

    /** @test */
    public function itDoesNotUpdateStatusIfAssignedDaysAgoIsBeforeAssignment()
    {
        $statusConfig = factory(StatusConfig::class)->state('in')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => [$this->oldStatus],
            'assigned_days_ago' => 3,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->oldStatus, $this->assignment->refresh()->status);
    }

    /** @test */
    public function itDoesNotUpdateStatusIfAssignedDaysAgoIsAfterAssignment()
    {
        $statusConfig = factory(StatusConfig::class)->state('in')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => [$this->oldStatus],
            'assigned_days_ago' => 1,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->oldStatus, $this->assignment->refresh()->status);
    }

    /** @test */
    public function itDoesNotUpdateStatusIfTypeIsInAndAssignmentStatusIsNotInProvided()
    {
        $statusConfig = factory(StatusConfig::class)->state('in')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => ['another_status'],
            'assigned_days_ago' => 2,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->oldStatus, $this->assignment->refresh()->status);
    }

    /** @test */
    public function itDoesNotUpdateStatusIfTypeIsOutAndAssignmentStatusIsInProvided()
    {
        $statusConfig = factory(StatusConfig::class)->state('out')->create([
            'office_id'         => $this->assignment->route->order->office_id,
            'new_status'        => $this->newStatus,
            'statuses'          => [$this->oldStatus],
            'assigned_days_ago' => 2,
        ]);

        SetAssignmentsStatuses::dispatchNow($statusConfig);

        $this->assertEquals($this->oldStatus, $this->assignment->refresh()->status);
    }
}
