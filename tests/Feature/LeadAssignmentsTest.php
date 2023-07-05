<?php

namespace Tests\Feature;

use App\Domain;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected $route;

    /**
     * Prepare test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete('Should be fixed');

        factory(Domain::class)->create([
            'linkType' => Domain::LANDING,
            'url'      => 'https://firepower.com',
            'offer_id' => factory(Offer::class)->create()->id,
        ]);
        $this->route = factory(LeadOrderRoute::class)->create([
            'leadsOrdered'  => 1,
            'leadsReceived' => 0,
            'offer_id'      => 1
        ]);
    }


    /** @test */
    public function itCreatesAssignment()
    {
        $this->postJson(route('leads.external'), [
            'firstname' => 'Yoko',
            'clickid'   => 'abdominal',
            'phone'     => '380664403003',
            'url'       => 'firepower.com',
        ]);

        $this->assertNotNull(LeadOrderAssignment::first(), 'assignment not created');
    }

    /** @test */
    public function itAssignsLeadToManager()
    {
        $this->postJson(route('leads.external'), [
            'firstname' => 'Yoko',
            'clickid'   => 'abdominal',
            'phone'     => '380664403003',
            'url'       => 'firepower.com',
        ]);

        $assignment = LeadOrderAssignment::first();
        $this->assertNotNull($assignment, 'assignment not created');
        $this->assertEquals($this->route->id, $assignment->route_id);
    }

    /** @test */
    public function itIncrementsOrderCounts()
    {
        $this->postJson(route('leads.external'), [
            'firstname' => 'Yoko',
            'clickid'   => 'abdominal',
            'phone'     => '380664403003',
            'url'       => 'firepower.com',
        ]);

        $this->assertEquals(1, $this->route->refresh()->leadsReceived);
        $this->assertTrue($this->route->isCompleted());
    }

    /** @test */
    public function itUpdatesLastReceived()
    {
        $this->postJson(route('leads.external'), [
            'firstname' => 'Yoko',
            'clickid'   => 'abdominal',
            'phone'     => '380664403003',
            'url'       => 'firepower.com',
        ]);

        $this->assertNotNull($this->route->refresh()->last_received_at);
    }
}
