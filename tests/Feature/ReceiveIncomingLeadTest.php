<?php

namespace Tests\Feature;

use App\Jobs\Leads\HandleBitrixUpdateHook;
use App\Jobs\Leads\SetBitrixComments;
use App\Jobs\Leads\SetBitrixStatus;
use App\Lead;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Services\Bitrix24\Bitrix24;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReceiveIncomingLeadTest extends TestCase
{
    use RefreshDatabase;

    protected $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = json_decode("{\"event\":\"ONCRMLEADUPDATE\",\"data\":{\"FIELDS\":{\"ID\":\"11590\"}},\"ts\":\"1596895662\",\"auth\":{\"domain\":\"crm-online.bitrix24.ua\",\"client_endpoint\":\"https:\/\/crm-online.bitrix24.ua\/rest\/\",\"server_endpoint\":\"https:\/\/oauth.bitrix.info\/rest\/\",\"member_id\":\"4a602fd9942dc4e76254c6da7f5ffd7e\",\"application_token\":\"2j1joo8giubjwgvmb5eqs30w1du5ywz0\"}}", true);

        $this->artisan('db:seed --class=LeadDestinationSeeder');
    }

    /** @test */
    public function itDispatchedJobWhichHandleUpdateLeadHook()
    {
        Queue::fake();
        Event::fake();

        factory(LeadOrderAssignment::class)->create([
            'external_id' => $this->data['data']['FIELDS']['ID'],
        ]);

        $this->post(route('hook.b24'), $this->data);

        Queue::assertPushed(HandleBitrixUpdateHook::class);
    }

    /** @test */
    public function itNotDispatchJobWhichHandleUpdateLeadHookIfEventIsWrong()
    {
        Queue::fake();
        Event::fake();

        factory(LeadOrderAssignment::class)->create([
            'external_id' => $this->data['data']['FIELDS']['ID'],
        ]);

        $this->post(route('hook.b24'), array_merge($this->data, [
            'event' => 'WRONGEVENT'
        ]));

        Queue::assertNotPushed(HandleBitrixUpdateHook::class);
    }

    /** @test */
    public function itNotDispatchJobWhichHandleUpdateLeadHookIfWeDontFindOurLead()
    {
        Queue::fake();
        Event::fake();

        factory(LeadOrderAssignment::class)->create([
            'external_id' => 50, // we dont have that lead
        ]);

        $this->post(route('hook.b24'), $this->data);

        Queue::assertNotPushed(HandleBitrixUpdateHook::class);
    }

    /** @test */
    public function weDontHandleHookIfLeadHasNotB24Destination()
    {
        Event::fake();

        // Wrong destination
        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => LeadDestination::whereDriver(LeadDestinationDriver::DEFAULT)->first()->id,
        ]);

        factory(LeadOrderAssignment::class)->create([
            'route_id'    => $route->id,
            'external_id' => $this->data['data']['FIELDS']['ID'],
        ]);

        $this->post(route('hook.b24'), $this->data);

        Queue::fake();
        Queue::assertNotPushed(SetBitrixStatus::class);
        Queue::assertNotPushed(SetBitrixComments::class);
    }

    /** @test */
    public function weHandleHookIfLeadHasB24Destination()
    {
        // TODO: find a way to mock this peace of shit
        $this->markTestIncomplete('how to mock this fucking service');
        Event::fake();

        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = ['url' => null];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        factory(LeadOrderAssignment::class)->create([
            'route_id'    => $route->id,
            'external_id' => $this->data['data']['FIELDS']['ID'],
        ]);

        $mock = $this->partialMock(Bitrix24::class);

        $mock->shouldReceive('getLead')->with('lead')->andReturn([
            'STATUS_ID' => 'testing',
            'COMMENTS'  => 'testing',
        ]);

        $mock->shouldReceive('getLeadComments')->with('lead')->andReturn([
            [
                'COMMENT' => 'comment',
                'CREATED' => now()->toDateTimeString(),
            ],
            [
                'COMMENT' => 'subminute comment',
                'CREATED' => now()->subMinute()->toDateTimeString(),
            ]
        ]);

        $lead_mock = $this->partialMock(LeadDestination::class);

        $lead_mock->shouldReceive('initialize')->andReturn($mock);

        $this->post(route('hook.b24'), $this->data)->dump();

        $this->assertTrue(true);
    }
}
