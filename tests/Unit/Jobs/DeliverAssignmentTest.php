<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DeliverAssignment;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliverAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRecordsExternalIdForAffBoat()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::AFFFBOAT)->first();

        $destination->configuration = ['link_id' => 0, 'token' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'id'        => $externalId,
                'success'   => true,
                'autologin' => '',
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForBitrix24()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::B24)->first();

        $destination->configuration = ['url' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'result' => $externalId,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForClickMate()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::CLICKMATE)->first();

        $destination->configuration = ['hash' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = sprintf('%s-%s', Str::random(), Str::random());
        Http::fake([
            '*' => Http::response([
                'data' => [
                    'lead'  => ['lead_id' => explode('-', $externalId)[0]],
                    'brand' => ['brand_id' => explode('-', $externalId)[1]],
                ],
                'status' => 200,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForEuroHot()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::EUROHOT)->first();

        $destination->configuration = ['affiliateId' => 0, 'partnerId' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'data' => [
                    'leadID' => $externalId,
                ],
                'returnCode' => 1,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForGlobalWise()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::GLOBALWISE)->first();

        $destination->configuration = ['affiliate_id' => 0, 'owner_id' => 0, 'username' => '', 'password' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'AccountId'   => $externalId,
                'IsSuccessed' => true,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForGoldver()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::GOLDVER)->first();

        $destination->configuration = ['campaign' => 0, 'token' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'client_id' => $externalId,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForHugeOffers()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::HUGEOFFERS)->first();

        $destination->configuration = ['token' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            'https://rest.messagebird.com/*' => Http::response([
                'countryPrefix' => 'ua',
            ]),
            '*' => Http::response([
                'click_id' => $externalId,
                'status'   => 3,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForOlympus()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::OLYMPUS)->first();

        $destination->configuration = ['apiKey' => 0, 'campaignId' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'client_id' => $externalId,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForStarLab()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::STARLAB)->first();

        $destination->configuration = ['user_id' => 0, 'hash' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'credentials' => ['id' => $externalId],
                'success'     => true,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }

    /** @test */
    public function itRecordsExternalIdForZurich()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::ZURICH)->first();

        $destination->configuration = ['username' => 0, 'password' => ''];
        $destination->save();

        $route = factory(LeadOrderRoute::class)->create([
            'destination_id' => $destination->id,
        ]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create(['route_id' => $route->id]);

        $this->assertNull($assignment->refresh()->external_id);

        $externalId = Str::random();
        Http::fake([
            '*' => Http::response([
                'profileUUID' => $externalId,
            ]),
        ]);

        DeliverAssignment::dispatchNow($assignment);

        $this->assertEquals($externalId, $assignment->refresh()->external_id);
    }
}
