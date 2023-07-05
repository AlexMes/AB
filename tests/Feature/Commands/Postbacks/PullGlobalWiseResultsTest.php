<?php

namespace Tests\Feature\Commands\Postbacks;

use App\Deposit;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PullGlobalWiseResultsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesDeposit()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::GLOBALWISE)->first();

        $destination->configuration = ['affiliate_id' => '', 'owner_id' => 0, 'username' => '', 'password' => ''];
        $destination->save();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'route_id'       => $route->id,
            'destination_id' => $destination->id,
            'external_id'    => Str::random(),
        ]);

        Http::fake([
            '*.united-asset-finance.com/Affiliate/api/home/itemsfor/Accounts*' => Http::response([
                'data' => [
                    ['Id' => $assignment->external_id, 'FtdAmount' => 123],
                ],
            ]),
            '*.united-asset-finance.com/api/affiliate/generateauthtoken' => Http::response([
                'token' => '',
            ]),
        ]);

        $this->artisan('postback:collect-globalwise-leads-results');

        $this->assertTrue($assignment->hasDeposit());
        $this->assertEquals(123, $assignment->getDeposit()->sum);
    }

    /** @test */
    public function itDoesNothingIfDepositExists()
    {
        Event::fake();
        Queue::fake();

        $this->seed(\LeadDestinationSeeder::class);
        $destination = LeadDestination::whereDriver(LeadDestinationDriver::GLOBALWISE)->first();

        $destination->configuration = ['affiliate_id' => '', 'owner_id' => 0, 'username' => '', 'password' => ''];
        $destination->save();

        $office  = factory(Office::class)->create();
        $manager = factory(Manager::class)->create(['office_id' => $office->id]);
        $order   = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route   = factory(LeadOrderRoute::class)->create(['manager_id' => $manager->id, 'order_id' => $order->id]);

        /** @var LeadOrderAssignment $assignment */
        $assignment = factory(LeadOrderAssignment::class)->create([
            'route_id'       => $route->id,
            'destination_id' => $destination->id,
            'external_id'    => Str::random(),
            'deposit_sum'    => 10101,
        ]);

        Deposit::createFromAssignment($assignment);

        Http::fake([
            '*.united-asset-finance.com/Affiliate/api/home/itemsfor/Accounts*' => Http::response([
                'data' => [
                    ['Id' => $assignment->external_id, 'FtdAmount' => 123],
                ],
            ]),
            '*.united-asset-finance.com/api/affiliate/generateauthtoken' => Http::response([
                'token' => '',
            ]),
        ]);

        $this->artisan('postback:collect-globalwise-leads-results');

        $this->assertDatabaseCount('deposits', 1);
        $this->assertEquals(10101, $assignment->getDeposit()->sum);
    }
}
