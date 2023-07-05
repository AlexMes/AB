<?php


namespace Tests\Unit\Models;

use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Office;
use App\OfficePayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LeadOrderRouteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itSkipsRoutesWithCompletedPaymentsOnOffice()
    {
        Event::fake();
        Bus::fake();

        $officeWithoutAnyPayments     = factory(Office::class)->create();
        $officeWithCompletedPayments  = factory(Office::class)->create();
        $officeWithIncompletePayments = factory(Office::class)->create();
        factory(OfficePayment::class)->state('completed')->create(['office_id' => $officeWithCompletedPayments->id]);
        factory(OfficePayment::class)->state('incomplete')->create(['office_id' => $officeWithIncompletePayments->id]);

        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithoutAnyPayments->id])->id,
        ]);
        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithCompletedPayments->id])->id,
        ]);
        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithIncompletePayments->id])->id,
        ]);

        $routes = LeadOrderRoute::excludeOfficesWithCompletedPayments()->get();
        $this->assertCount(2, $routes);
        $this->assertCount(0, $routes->where('order.office_id', $officeWithCompletedPayments->id));
        /*$this->assertCount(1, $routes->where('order.office_id', $officeWithoutAnyPayments->id));
        $this->assertCount(0, $routes->where('order.office_id', $officeWithCompletedPayments->id));
        $this->assertCount(1, $routes->where('order.office_id', $officeWithIncompletePayments->id));*/
    }

    /** @test */
    public function itDoesNotSkipRoutesWithIncompletePaymentsOnOffice()
    {
        Event::fake();
        Bus::fake();

        $officeWithoutAnyPayments     = factory(Office::class)->create();
        $officeWithCompletedPayments  = factory(Office::class)->create();
        $officeWithIncompletePayments = factory(Office::class)->create();
        factory(OfficePayment::class)->state('completed')->create(['office_id' => $officeWithCompletedPayments->id]);
        factory(OfficePayment::class)->state('incomplete')->create(['office_id' => $officeWithIncompletePayments->id]);

        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithoutAnyPayments->id])->id,
        ]);
        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithCompletedPayments->id])->id,
        ]);
        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithIncompletePayments->id])->id,
        ]);

        $routes = LeadOrderRoute::excludeOfficesWithCompletedPayments()->get();
        $this->assertCount(2, $routes);
        $this->assertCount(1, $routes->where('order.office_id', $officeWithIncompletePayments->id));
    }

    /** @test */
    public function itDoesNotSkipRoutesWithNoPaymentsOnOffice()
    {
        Event::fake();
        Bus::fake();

        $officeWithoutAnyPayments     = factory(Office::class)->create();
        $officeWithCompletedPayments  = factory(Office::class)->create();
        $officeWithIncompletePayments = factory(Office::class)->create();
        factory(OfficePayment::class)->state('completed')->create(['office_id' => $officeWithCompletedPayments->id]);
        factory(OfficePayment::class)->state('incomplete')->create(['office_id' => $officeWithIncompletePayments->id]);

        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithoutAnyPayments->id])->id,
        ]);
        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithCompletedPayments->id])->id,
        ]);
        factory(LeadOrderRoute::class)->create([
            'order_id' => factory(LeadsOrder::class)->create(['office_id' => $officeWithIncompletePayments->id])->id,
        ]);

        $routes = LeadOrderRoute::excludeOfficesWithCompletedPayments()->get();
        $this->assertCount(2, $routes);
        $this->assertCount(1, $routes->where('order.office_id', $officeWithoutAnyPayments->id));
    }
}
