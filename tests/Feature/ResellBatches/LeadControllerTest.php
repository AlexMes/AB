<?php

namespace Tests\Feature\ResellBatches;

use App\CRM\Age;
use App\CRM\Status;
use App\Lead;
use App\Offer;
use App\Office;
use App\ResellBatch;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuthOnCreate()
    {
        Event::fake();

        $this->assertGuest();

        $this->postJson(route('resell-batches.leads.store', ['resell_batch' => 1]))
            ->assertStatus(401);
    }

    /** @test  */
    public function buyerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('buyer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function customerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('customer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function farmerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('farmer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function financierHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('financier')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function designerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('designer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function teamleadHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('teamlead')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function verifierHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('verifier')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function gamblerHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('gambler')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(401);
    }

    /** @test  */
    public function gambleAdminHasNoAccessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('gamble-admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(401);
    }

    /** @test  */
    public function registeredAtIsRequiredOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at');
    }

    /** @test  */
    public function registeredAtMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'registered_at' => now()->toDateString(),
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at');
    }

    /** @test  */
    public function registeredAtMustHaveTwoElementsOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'registered_at' => [now()->toDateString()],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at');
    }

    /** @test  */
    public function registeredAtMustHaveDateFormatOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'registered_at' => [123, 123],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at.0')
            ->assertJsonValidationErrors('registered_at.1');
    }

    /** @test  */
    public function registeredAtUntilShouldBeBeforeOrEqualTodayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'registered_at' => [now()->toDateString(), now()->addDays()->toDateString()],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at.1')
            ->assertJsonMissingValidationErrors('registered_at.0');
    }

    /** @test  */
    public function registeredAtSinceShouldBeBeforeOrEqualUntilOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'registered_at' => [now()->toDateString(), now()->subDays()->toDateString()],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('registered_at.0');
    }




    /** @test  */
    public function createdAtCanBeNullOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'created_at' => null,
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('created_at');
    }

    /** @test  */
    public function createdAtMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'created_at' => now()->toDateString(),
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at');
    }

    /** @test  */
    public function createdAtMustHaveTwoElementsOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'created_at' => [now()->toDateString()],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at');
    }

    /** @test  */
    public function createdAtMustHaveDateFormatOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'created_at' => [123, 123],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at.0')
            ->assertJsonValidationErrors('created_at.1');
    }

    /** @test  */
    public function createdAtUntilShouldBeBeforeOrEqualTodayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'created_at' => [now()->toDateString(), now()->addDays()->toDateString()],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at.1')
            ->assertJsonMissingValidationErrors('created_at.0');
    }

    /** @test  */
    public function createdAtSinceShouldBeBeforeOrEqualUntilOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'created_at' => [now()->toDateString(), now()->subDays()->toDateString()],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('created_at.0')
            ->assertJsonMissingValidationErrors('created_at.1');
    }



    /** @test  */
    public function officeCanBeMissedOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('office');
    }

    /** @test  */
    public function officeMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'office' => factory(Office::class)->create()->id,
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('office');
    }

    /** @test  */
    public function officeMustExistOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'office' => [123, 124],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('office.0')
            ->assertJsonValidationErrors('office.1');
    }



    /** @test  */
    public function offerCanBeMissedOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('offer');
    }

    /** @test  */
    public function offerMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'offer' => factory(Offer::class)->create()->id,
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('offer');
    }

    /** @test  */
    public function offerMustExistOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'offer' => [123, 124],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('offer.0')
            ->assertJsonValidationErrors('offer.1');
    }



    /** @test  */
    public function statusCanBeMissedOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('status');
    }

    /** @test  */
    public function statusMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'status' => Status::first()->name,
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    /** @test  */
    public function statusMustExistOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'status' => [123, 124],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('status.0')
            ->assertJsonValidationErrors('status.1');
    }



    /** @test  */
    public function ageCanBeMissedOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors('age');
    }

    /** @test  */
    public function ageMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'age' => Age::first()->name,
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('age');
    }

    /** @test  */
    public function ageMustExistOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'age' => [123, 124],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('age.0')
            ->assertJsonValidationErrors('age.1');
    }

    /** @test  */
    public function leadsAreRequiredOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('resell-batches.leads.store', $batch))
            ->assertStatus(422)
            ->assertJsonValidationErrors('leads');
    }

    /** @test  */
    public function leadsMustBeArrayOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'leads' => 123,
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('leads');
    }

    /** @test  */
    public function leadsMustExistOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();
        $lead  = factory(Lead::class)->create();

        $data = [
            'leads' => [123, $lead->id],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('leads.0')
            ->assertJsonMissingValidationErrors('leads.1');
    }

    /** @test */
    public function statusCannotBeInProcessOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->state('in-process')->create();
        $lead  = factory(Lead::class)->create();

        $data = [
            'leads' => [$lead->id],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('leads');
    }

    /** @test */
    public function statusCannotBeFinishedOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->state('finished')->create();
        $lead  = factory(Lead::class)->create();

        $data = [
            'leads' => [$lead->id],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('leads');
    }

    /** @test */
    public function itSavesFiltersOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->state('pending')->create();
        $lead  = factory(Lead::class)->create();

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'leads'         => [$lead->id],
        ];

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(201);

        $this->assertEquals(Arr::except($data, 'leads'), $batch->refresh()->filters);
    }

    /** @test */
    public function itAppendsLeadsOnCreate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->state('pending')->create();
        $lead  = factory(Lead::class)->create();

        $data = [
            'registered_at' => [now()->toDateString(), now()->toDateString()],
            'leads'         => [$lead->id],
        ];

        $this->assertCount(0, $batch->leads);

        $this->postJson(route('resell-batches.leads.store', $batch), $data)
            ->assertStatus(201);

        $this->assertCount(1, $batch->fresh()->leads);
    }
}
