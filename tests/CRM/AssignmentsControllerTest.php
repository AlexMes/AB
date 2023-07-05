<?php

namespace Tests\CRM;

use App\CRM\Age;
use App\CRM\Profession;
use App\CRM\Reason;
use App\Deposit;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AssignmentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function assignmentCannotBeChangedIfStatusDepositOnCrmGuard()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);

        $this->setGuard('crm')->signIn($manager);

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'status' => 'Депозит']);

        $this->put(route('crm.assignments.update', $assignment))
            ->assertSessionHasErrors(['status_deposit_blocked']);
    }

    /** @test */
    public function assignmentCanBeChangedIfStatusDepositOnWebGuard()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $this->setGuard('web')->signIn();

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'status' => 'Депозит']);

        $this->put(route('crm.assignments.update', $assignment))
            ->assertSessionDoesntHaveErrors(['status_deposit_blocked']);
    }

    /** @test */
    public function itCreatesDepositOnUpdate()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $this->setGuard('web')->signIn();

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);
        $this->assertFalse($assignment->hasDeposit());

        $this->put(
            route('crm.assignments.update', $assignment),
            [
                'status'      => 'Депозит',
                'deposit_sum' => 300,
                'gender_id'   => 0,
                'age'         => Age::first()->name,
                'profession'  => Profession::first()->name,
            ]
        )->assertSessionHasNoErrors();

        $this->assertTrue($assignment->hasDeposit());
    }

    /** @test */
    public function itDoesNothingWithDepositIfItExistsOnUpdate()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $this->setGuard('web')->signIn();

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'deposit_sum' => 100]);
        Deposit::createFromAssignment($assignment);

        $this->put(
            route('crm.assignments.update', $assignment),
            [
                'status'      => 'Депозит',
                'deposit_sum' => 300,
                'gender_id'   => 0,
                'age'         => Age::first()->name,
                'profession'  => Profession::first()->name,
            ]
        )->assertSessionHasNoErrors();

        $this->assertEquals(100, $assignment->getDeposit()->sum);
    }

    /** @test */
    public function itUpdatesAssignmentOnUpdate()
    {
        Queue::fake();
        Event::fake();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $this->setGuard('web')->signIn();

        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id, 'status' => 'Новый']);

        $this->put(
            route('crm.assignments.update', $assignment),
            [
                'status'        => 'Отказ',
                'reject_reason' => Reason::first()->name,
                'gender_id'     => 0,
                'age'           => Age::first()->name,
                'profession'    => Profession::first()->name,
            ]
        )->assertSessionHasNoErrors();

        $this->assertEquals('Отказ', $assignment->refresh()->status);
    }
}
