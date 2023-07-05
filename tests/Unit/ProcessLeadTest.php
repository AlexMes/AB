<?php

namespace Tests\Unit;

use App\Domain;
use App\Jobs\ProcessLead;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessLeadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itSavesFuckingLead()
    {
        $d = factory(Domain::class)->create();

        $lead = ['firstname' => 'Test', 'domain' => $d->url, 'phone' => '9379992'];

        ProcessLead::dispatch($lead);

        $this->assertDatabaseHas('leads', ['phone' => '9379992']);
    }

    /** @test */
    public function itDeterminesLanding()
    {
        $d = factory(Domain::class)
            ->state(Domain::LANDING)
            ->create(['url' => 'https://ton-huem.com']);

        $lead = ['firstname' => 'Test', 'domain' => 'ton-huem.com', 'phone' => '9379992'];

        ProcessLead::dispatch($lead);

        $this->assertDatabaseHas('leads', ['phone' => '9379992','landing_id' => $d->id]);
    }

    /** @test */
    public function itAllowsSameLeadFromDifferentDomains()
    {
        $d = factory(Domain::class)
            ->state(Domain::LANDING)
            ->create(['url' => 'https://ton-huem.com']);

        $lead = ['firstname' => 'Test', 'domain' => 'ton-huem.com', 'phone' => '9379992'];

        $d2 = factory(Domain::class)
            ->state(Domain::LANDING)
            ->create(['url' => 'https://ton-yobom.com']);

        $lead2 = ['firstname' => 'Test', 'domain' => 'ton-yobom.com', 'phone' => '9379992'];

        ProcessLead::dispatch($lead);
        ProcessLead::dispatch($lead2);

        $this->assertDatabaseHas('leads', ['phone' => '9379992','landing_id' => $d->id]);
        $this->assertDatabaseHas('leads', ['phone' => '9379992','landing_id' => $d2->id]);

        $this->assertEquals(2, Lead::count());
    }

    /** @test */
    public function itRejectsSamePhoneFromSameDomain()
    {
        $domain = factory(Domain::class)
            ->state(Domain::LANDING)
            ->create(['url' => 'https://ton-huem.com']);

        $lead  = ['firstname' => 'Test', 'domain' => 'ton-huem.com', 'phone' => '9379992'];
        $lead2 = ['firstname' => 'Test', 'domain' => 'ton-huem.com', 'phone' => '9379992'];

        ProcessLead::dispatch($lead);
        ProcessLead::dispatch($lead2);

        $this->assertDatabaseHas('leads', ['phone' => '9379992','landing_id' => $domain->id]);

        $this->assertEquals(1, Lead::count());
    }

    /** @test */
    public function itRejectsSameClickIdFromSameDomain()
    {
        $domain = factory(Domain::class)
            ->state(Domain::LANDING)
            ->create(['url' => 'https://ton-huem.com']);

        $lead  = ['firstname' => 'Test', 'domain' => 'ton-huem.com', 'clickid' => '9379992', 'phone' => '9379992'];
        $lead2 = ['firstname' => 'Test', 'domain' => 'ton-huem.com', 'clickid' => '9379992', 'phone' => '9379992'];

        ProcessLead::dispatch($lead);
        ProcessLead::dispatch($lead2);

        $this->assertDatabaseHas('leads', ['clickid' => '9379992','landing_id' => $domain->id]);

        $this->assertEquals(1, Lead::count());
    }
}
