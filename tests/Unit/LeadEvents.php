<?php

namespace Tests\Unit;

use App\Affiliate;
use App\Binom;
use App\Domain;
use App\Events\AssignmentTransferred;
use App\Facebook\Account;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Jobs\Leads\DetectAccount;
use App\Jobs\Leads\DetectBuyer;
use App\Jobs\Leads\DetectCampaign;
use App\Jobs\Leads\DetectGender;
use App\Jobs\Leads\DetectOffer;
use App\Jobs\Leads\DetectTrafficSource;
use App\Jobs\Leads\FetchIpAddressData;
use App\Jobs\PullClickInfo;
use App\Jobs\TransferLeadOrderRoute;
use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Manager;
use App\Services\GenderApi\GenderAPI;
use App\Services\IpApi\IpApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class LeadEvents extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCreatesCreatedEventOnCreated()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $this->assertCount(1, $lead->events->where('type', 'created'));
    }

    /** @test */
    public function itCreatesUpdatedEventOnUpdated()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->update(['firstname' => Str::random()]);

        $this->assertCount(2, $lead->events);
        $this->assertCount(1, $lead->events->where('type', 'updated'));
    }

    /** @test */
    public function itCreatesDeletedEventOnDeleted()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->delete();

        $this->assertCount(2, $lead->events);
        $this->assertCount(1, $lead->events->where('type', 'deleted'));
    }

    /** @test */
    public function itContainsUserDataOnCreate()
    {
        Queue::fake();

        $this->signIn();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'created',
            'auth_id'        => $this->user->id,
            'auth_type'      => get_class($this->user),
        ]);
    }

    /** @test */
    public function itContainsUserDataOnUpdate()
    {
        Queue::fake();

        $this->signIn();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->update(['firstname' => Str::random()]);

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'updated',
            'auth_id'        => $this->user->id,
            'auth_type'      => get_class($this->user),
        ]);
    }

    /** @test */
    public function itContainsUserDataOnDelete()
    {
        Queue::fake();

        $this->signIn();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->delete();

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'deleted',
            'auth_id'        => $this->user->id,
            'auth_type'      => get_class($this->user),
        ]);
    }

    /** @test */
    public function itContainsManagerDataOnCreate()
    {
        Queue::fake();

        $manager = factory(Manager::class)->create();
        $this->signIn($manager);

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'created',
            'auth_id'        => $manager->id,
            'auth_type'      => get_class($manager),
        ]);
    }

    /** @test */
    public function itContainsManagerDataOnUpdate()
    {
        Queue::fake();

        $manager = factory(Manager::class)->create();
        $this->signIn($manager);

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->update(['firstname' => Str::random()]);

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'updated',
            'auth_id'        => $manager->id,
            'auth_type'      => get_class($manager),
        ]);
    }

    /** @test */
    public function itContainsManagerDataOnDelete()
    {
        Queue::fake();

        $manager = factory(Manager::class)->create();
        $this->signIn($manager);

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->delete();

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'deleted',
            'auth_id'        => $manager->id,
            'auth_type'      => get_class($manager),
        ]);
    }

    /** @test */
    public function itContainsOriginalDataOnlyWhatWasChanged()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $oldName = $lead->firstname;
        $lead->update(['firstname' => Str::random()]);

        $event = $lead->events->where('type', 'updated')->first();

        $this->assertCount(1, $event->original_data);
        $this->assertArrayHasKey('firstname', $event->original_data);
        $this->assertEquals($oldName, $event->original_data['firstname']);
    }

    /** @test */
    public function itContainsOriginalDataOnDelete()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->delete();

        $event = $lead->events->where('type', 'deleted')->first();

        $this->assertCount(0, $event->changed_data);
        $this->assertCount(count($lead->getOriginal()), $event->original_data);
    }

    /** @test */
    public function itContainsChangedDataOnCreate()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->make();
        $data = $lead->setAppends([])->toArray();
        $lead->save();

        $event = $lead->events->where('type', 'created')->first();

        $this->assertCount(0, array_diff($data, $event->changed_data));
        $this->assertCount(0, $event->original_data);
    }

    /** @test */
    public function itContainsChangedDataOnUpdate()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->update(['firstname' => Str::random()]);

        $event = $lead->events->where('type', 'updated')->first();

        $this->assertCount(1, $event->changed_data);
        $this->assertArrayHasKey('firstname', $event->changed_data);
        $this->assertEquals($lead->firstname, $event->changed_data['firstname']);
    }

    /** @test */
    public function itCanBeRecordedAsCustomTypeOnCreate()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->make();
        $lead->recordAs('new_lead_created')->save();

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'new_lead_created',
        ]);
    }

    /** @test */
    public function itCanBeRecordedAsCustomTypeOnUpdate()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->recordAs('custom_action_type')->update(['firstname' => Str::random()]);

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'custom_action_type',
        ]);
    }

    /** @test */
    public function itCanBeRecordedAsCustomTypeOnDelete()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->recordAs('custom_delete_action')->delete();

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'custom_delete_action',
        ]);
    }

    /** @test */
    public function itCanBeAddedAsCustomEvent()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $lead->addEvent('custom_event');

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'custom_event',
        ]);
    }

    /** @test */
    public function customEventContainsCustomData()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $customData = ['any_field' => 'any_value'];
        $lead->addEvent('custom_event', $customData);

        $this->assertDatabaseHas('events', [
            'eventable_id'   => $lead->id,
            'eventable_type' => $lead->events()->getMorphClass(),
            'type'           => 'custom_event',
            'custom_data'    => json_encode($customData),
        ]);
    }

    /** @test */
    public function customEventContainsOriginalData()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $originalData = ['any_field' => 'any_value'];
        $lead->addEvent('custom_event', null, $originalData);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => 'custom_event',
            'original_data'    => json_encode($originalData),
        ]);
    }

    /** @test */
    public function itCreatesEventOnOfferDetected()
    {
        Queue::fake();

        $affiliate = factory(Affiliate::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['affiliate_id' => $affiliate->id]);

        DetectOffer::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::OFFER_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnAccountDetected()
    {
        Queue::fake();

        $campaign = factory(Campaign::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['campaign_id' => $campaign->id]);

        DetectAccount::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::ACCOUNT_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnBuyerDetected()
    {
        Queue::fake();

        $account = factory(Account::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['account_id' => $account->account_id]);

        DetectBuyer::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::BUYER_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnGenderDetected()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $this->mock(GenderAPI::class, function (MockInterface $mock) {
            $mock->shouldReceive('detect')
                ->once()
                ->andReturn(1);
        });
        DetectGender::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::GENDER_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnIpFetched()
    {
        Queue::fake();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $this->mock(IpApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->andReturn(null);
        });
        FetchIpAddressData::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::IP_FETCHED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnClickInfoPulled()
    {
        $this->markTestIncomplete('Not a clue how to make it work (');
        Queue::fake();

        $binom = factory(Binom::class)->state('enabled')->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create();

        $this->mock(Binom::class, function (MockInterface $mock) use ($binom) {
            $mock->shouldReceive('getClick')
                ->andReturn([]);
        });
        PullClickInfo::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::CLICK_INFO_PULLED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnCampaignDetectedViaCampaign()
    {
        Queue::fake();

        $campaign = factory(Campaign::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['utm_source' => $campaign->name]);

        DetectCampaign::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::CAMPAIGN_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnCampaignDetectedViaAdSet()
    {
        Queue::fake();

        $adset = factory(AdSet::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['utm_content' => $adset->name]);

        DetectCampaign::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::CAMPAIGN_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnAssigned()
    {
        Queue::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->state('incomplete')->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['offer_id'  => $route->offer_id]);

        $this->mock(Pipeline::class, function (MockInterface $mock) use ($lead) {
            $mock->shouldReceive('send')->passthru();

            $mock->shouldReceive('through')->andReturnSelf();

            $mock->shouldReceive('then')->passthru();
        });
        LeadAssigner::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::ASSIGNED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnTransferred()
    {
        Queue::fake();

        /** @var LeadOrderAssignment $assignment */
        $assignment    = factory(LeadOrderAssignment::class)->create();
        $targetManager = factory(Manager::class)->create();

        Event::fake(AssignmentTransferred::class);
        $assignment->transfer($targetManager);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $assignment->lead->id,
            'eventable_type'   => $assignment->lead->events()->getMorphClass(),
            'type'             => Lead::TRANSFERRED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnRouteTransferred()
    {
        Queue::fake();

        /** @var LeadOrderRoute $route */
        $route = factory(LeadOrderRoute::class)->create();

        $assignments = factory(LeadOrderAssignment::class, 2)->create(['route_id' => $route->id]);
        factory(LeadOrderAssignment::class, 3)->create();

        $targetManager = factory(Manager::class)->create();

        TransferLeadOrderRoute::dispatchNow($route, $targetManager);

        $assignments->each(function (LeadOrderAssignment $assignment) {
            $this->assertDatabaseHas('events', [
                'eventable_id'     => $assignment->lead->id,
                'eventable_type'   => $assignment->lead->events()->getMorphClass(),
                'type'             => Lead::ROUTE_TRANSFERRED,
            ]);
        });
    }

    /** @test */
    public function itCreatesEventOnTrafficSourceDetectedViaLanding()
    {
        Queue::fake();

        $landing = factory(Domain::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['landing_id' => $landing->id]);

        DetectTrafficSource::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::TRAFFIC_SOURCE_DETECTED,
        ]);
    }

    /** @test */
    public function itCreatesEventOnTrafficSourceDetectedViaAffiliate()
    {
        Queue::fake();

        $affiliate = factory(Affiliate::class)->create();

        /** @var Lead $lead */
        $lead = factory(Lead::class)->create(['affiliate_id' => $affiliate->id]);

        DetectTrafficSource::dispatchNow($lead);

        $this->assertDatabaseHas('events', [
            'eventable_id'     => $lead->id,
            'eventable_type'   => $lead->events()->getMorphClass(),
            'type'             => Lead::TRAFFIC_SOURCE_DETECTED,
        ]);
    }
}
