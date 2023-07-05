<?php

namespace Tests\Feature\Commands;

use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\LeadOrderAssignment;
use App\Office;
use App\ResellBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RunResellBatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDoesNotRunPendingBatch()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('pending')->create();
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertNotDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itDoesNotRunFinishedBatch()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('finished')->create();
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertNotDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itDoesNotRunCompletedBatch()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create();
        $batch->leads()->attach(factory(Lead::class)->create(), ['assigned_at' => now()]);

        $this->artisan('resell-batch:run');

        Bus::assertNotDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itRunsInProcessBatch()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHour()->addMinute(),
        ]);
        $lead = factory(Lead::class)->create();
        $batch->leads()->attach($lead);

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class, fn ($job) => $job->lead->is($lead));
    }

    /** @test */
    public function itDispatchesJobIfAssignUntilIsBeforeNow()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->subHour(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itDispatchesJobIfAssignUntilIsNow()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itDispatchesJobIfAssignUntilIsNull()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => null,
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itDoesNotDispatchJobIfPeriodIsTooLarge()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHours(3)->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertNotDispatched(LeadAssigner::class);
    }

    /** @test */
    public function itDispatchesJobIfPivotAssignedAtIsNull()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());
        $batch->leads()->attach(factory(Lead::class)->create(), ['assigned_at' => now()]);

        $this->artisan('resell-batch:run');

        Bus::assertDispatchedTimes(LeadAssigner::class, 1);
    }

    /** @test */
    public function itSeparatesDispatchingWhenPeriodIsLargeEnough()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHours(2)->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertDispatchedTimes(LeadAssigner::class, 1);
    }

    /** @test */
    public function itPassesOfficesToJob()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHour()->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());
        $batch->offices()->attach(factory(Office::class)->create());
        factory(Office::class)->create();

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(
            LeadAssigner::class,
            function ($job) use ($batch) {
                $jobOffices = tap((new \ReflectionClass($job))->getProperty('only'))
                    ->setAccessible(true)
                    ->getValue($job);

                return count($jobOffices) === 1 && $jobOffices[0] === $batch->offices->first()->id;
            }
        );
    }

    /** @test */
    public function itPassesRegisteredAtToJobIfRegDateIsLessToday()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until'  => now()->addHour()->addMinute(),
            'registered_at' => now()->subDay(),
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['registered_at' => now()->subDays(15)->setHours(1)]);
        $batch->leads()->attach($assignment->lead);

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class, function (LeadAssigner $job) use ($batch, $assignment) {
            return $job->registeredAt->year === $batch->registered_at->year
                && $job->registeredAt->month === $batch->registered_at->month
                && $job->registeredAt->day === $batch->registered_at->day
                && $job->registeredAt->hour === $assignment->registered_at->hour
                && $job->registeredAt->minute === $assignment->registered_at->minute
                && $job->registeredAt->second === $assignment->registered_at->second;
        });
    }

    /** @test */
    public function itPassesRegisteredAtToJobIfRegDateIsToday()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until'  => now()->addHour()->addMinute(),
            'registered_at' => now()->subHours()->subMinutes(5),
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['registered_at' => now()->subDays(15)->setHours(1)]);
        $batch->leads()->attach($assignment->lead);

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class, function (LeadAssigner $job) use ($batch, $assignment) {
            return $job->registeredAt->year === $batch->registered_at->year
                && $job->registeredAt->month === $batch->registered_at->month
                && $job->registeredAt->day === $batch->registered_at->day
                && $job->registeredAt->hour === now()->hour
                && $job->registeredAt->minute === now()->minute;
        });
    }

    /** @test */
    public function itUpdatesPivotAssignedAt()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHour()->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());
        $this->assertNull($batch->leads->first()->pivot->assigned_at);

        $this->artisan('resell-batch:run');

        $this->assertNotNull($batch->fresh(['leads'])->leads->first()->pivot->assigned_at);
    }

    /** @test */
    public function itMarksBatchAsFinishedIfNoLeadLeft()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHour()->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());
        $this->assertEquals(ResellBatch::IN_PROCESS, $batch->status);

        $this->artisan('resell-batch:run');

        $this->assertEquals(ResellBatch::FINISHED, $batch->refresh()->status);
    }

    /** @test */
    public function itDoesNotMarkBatchAsFinishedIfLeadsLeft()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until' => now()->addHours(2)->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->assertEquals(ResellBatch::IN_PROCESS, $batch->status);

        $this->artisan('resell-batch:run');

        $this->assertEquals(ResellBatch::IN_PROCESS, $batch->refresh()->status);
    }

    /** @test */
    public function itDispatchesJobAsDelayed()
    {
        Event::fake();
        Bus::fake();

        /** @var ResellBatch $batch */
        $batch = factory(ResellBatch::class)->state('in-process')->create([
            'assign_until'  => now()->addHour()->addMinute(),
        ]);
        $batch->leads()->attach(factory(Lead::class)->create());

        $this->artisan('resell-batch:run');

        Bus::assertDispatched(LeadAssigner::class, fn ($job) => !is_null($job->delay));
    }
}
