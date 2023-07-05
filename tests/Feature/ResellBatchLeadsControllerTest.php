<?php

namespace Tests\Feature;

use App\CRM\Age;
use App\CRM\Status;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadsOrder;
use App\Offer;
use App\Office;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ResellBatchLeadsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuthOnCreate()
    {
        Event::fake();

        $this->assertGuest();

        $this->getJson(route('resell-batch-leads', ['resell_batch' => 1]))
            ->assertStatus(401);
    }

    /** @test  */
    public function buyerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('buyer')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function customerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('customer')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function farmerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('farmer')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function financierHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('financier')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function designerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('designer')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function teamleadHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('teamlead')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function verifierHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('verifier')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(403);
    }

    /** @test  */
    public function gamblerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('gambler')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(401);
    }

    /** @test  */
    public function gambleAdminHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('gamble-admin')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(401);
    }



    /** @test  */
    public function filtersRegisteredAtIsRequired()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at');
    }

    /** @test  */
    public function filtersRegisteredAtMustBeArray()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => now()->toDateString(),
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at');
    }

    /** @test  */
    public function filtersRegisteredAtMustHaveTwoElements()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString()],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at');
    }

    /** @test  */
    public function filtersRegisteredAtMustHaveDateFormat()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [123, 123],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at.0')
            ->assertJsonValidationErrors('registered_at.1');
    }

    /** @test  */
    public function filtersRegisteredAtUntilShouldBeBeforeOrEqualToday()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString(), now()->addDays()->toDateString()],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at.1')
            ->assertJsonMissingValidationErrors('registered_at.0');
    }

    /** @test  */
    public function filtersRegisteredAtSinceShouldBeBeforeOrEqualUntil()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString(), now()->subDays()->toDateString()],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at.0');
    }




    /** @test  */
    public function filtersCreatedAtCanBeNull()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'created_at' => null,
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('created_at');
    }

    /** @test  */
    public function filtersCreatedAtMustBeArray()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'created_at' => now()->toDateString(),
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at');
    }

    /** @test  */
    public function filtersCreatedAtMustHaveTwoElements()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'created_at' => [now()->toDateString()],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at');
    }

    /** @test  */
    public function filtersCreatedAtMustHaveDateFormat()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'created_at' => [123, 123],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at.0')
            ->assertJsonValidationErrors('created_at.1');
    }

    /** @test  */
    public function filtersCreatedAtUntilShouldBeBeforeOrEqualToday()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'created_at' => [now()->toDateString(), now()->addDays()->toDateString()],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at.1')
            ->assertJsonMissingValidationErrors('created_at.0');
    }

    /** @test  */
    public function filtersCreatedAtSinceShouldBeBeforeOrEqualUntil()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'created_at' => [now()->toDateString(), now()->subDays()->toDateString()],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at.0')
            ->assertJsonMissingValidationErrors('created_at.1');
    }



    /** @test  */
    public function filtersOfficeCanBeMissed()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('office');
    }

    /** @test  */
    public function filtersOfficeMustBeArray()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'office' => factory(Office::class)->create()->id,
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('office');
    }

    /** @test  */
    public function filtersOfficeMustExist()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'office' => [123, 124],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('office.0')
            ->assertJsonValidationErrors('office.1');
    }



    /** @test  */
    public function filtersOfferCanBeMissed()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('offer');
    }

    /** @test  */
    public function filtersOfferMustBeArray()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'offer' => factory(Offer::class)->create()->id,
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('offer');
    }

    /** @test  */
    public function filtersOfferMustExist()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'offer' => [123, 124],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('offer.0')
            ->assertJsonValidationErrors('offer.1');
    }



    /** @test  */
    public function filtersStatusCanBeMissed()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('status');
    }

    /** @test  */
    public function filtersStatusMustBeArray()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'status' => Status::first()->name,
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    /** @test  */
    public function filtersStatusMustExist()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'status' => [123, 124],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('status.0')
            ->assertJsonValidationErrors('status.1');
    }



    /** @test  */
    public function filtersAgeCanBeMissed()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $this->getJson(route('resell-batch-leads'))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('age');
    }

    /** @test  */
    public function filtersAgeMustBeArray()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'age' => Age::first()->name,
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('age');
    }

    /** @test  */
    public function filtersAgeMustExist()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'age' => [123, 124],
        ];

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(422)
            ->assertJsonValidationErrors('age.0')
            ->assertJsonValidationErrors('age.1');
    }



    /** @test */
    public function itFiltersByRegisteredAt()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->subDays()->toDateString(), now()->subDays()->toDateString()],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create(['registered_at' => now()->toDateString()]);
        $assignment       = factory(LeadOrderAssignment::class)->create(['registered_at' => now()->subDays()]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }

    /** @test */
    public function itFiltersByCreatedAt()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'created_at'    => [now()->subDays()->toDateString(), now()->subDays()->toDateString()],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'created_at'    => now()->toDateString(),
        ]);
        $assignment       = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'created_at'    => now()->subDays()
        ]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }

    /** @test */
    public function itFiltersByOffice()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $office = factory(Office::class)->create();
        $order  = factory(LeadsOrder::class)->create(['office_id' => $office->id]);
        $route  = factory(LeadOrderRoute::class)->create(['order_id' => $order->id]);

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'office'        => [$office->id],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
        ]);

        $assignment       = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'route_id'      => $route->id,
        ]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }

    /** @test */
    public function itFiltersByOffer()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $route  = factory(LeadOrderRoute::class)->create();

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'offer'         => [$route->offer_id],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
        ]);

        $assignment       = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'route_id'      => $route->id,
        ]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }

    /** @test */
    public function itFiltersByStatus()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'status'        => ['Отказ'],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
        ]);

        $assignment       = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'status'        => 'Отказ',
        ]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }

    /** @test */
    public function itFiltersNullStatusAsNew()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'status'        => ['Новый'],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'status'        => 'Перезвон',
        ]);

        $assignment       = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
        ]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }

    /** @test */
    public function itFiltersByAge()
    {
        Event::fake();
        Queue::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'age'           => ['18-24'],
        ];

        $missedAssignment = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
        ]);

        $assignment       = factory(LeadOrderAssignment::class)->create([
            'registered_at' => now()->toDateString(),
            'age'           => '18-24',
        ]);

        $this->getJson(route('resell-batch-leads', $data))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([$assignment->lead->toArray()]);
    }
}
