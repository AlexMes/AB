<?php

namespace Tests\Feature\ResellBatches;

use App\ResellBatch;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itRequiresAuth()
    {
        Event::fake();

        $this->assertGuest();

        $this->postJson(route('start-resell-batch', ['resell_batch' => 1]))
            ->assertStatus(401);
    }

    /** @test  */
    public function buyerHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('buyer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function customerHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('customer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function farmerHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('farmer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function financierHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('financier')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function designerHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('designer')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function teamleadHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('teamlead')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function verifierHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('verifier')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(403);
    }

    /** @test  */
    public function gamblerHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('gambler')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(401);
    }

    /** @test  */
    public function gambleAdminHasNoAccess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('gamble-admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(401);
    }

    /** @test */
    public function itRequiresAssignUntil()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $this->postJson(route('start-resell-batch', $batch))
            ->assertStatus(422)
            ->assertJsonValidationErrors('assign_until');
    }

    /** @test */
    public function assignUntilMustHaveAValidDateFormat()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'assign_until' => 'invalid date',
        ];

        $this->postJson(route('start-resell-batch', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('assign_until');
    }

    /** @test */
    public function assignUntilMustBeAfterDate()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();

        $data = [
            'assign_until' => now()->addHour()->subMinute()->format('Y-m-d H:i'),
        ];

        $this->postJson(route('start-resell-batch', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('assign_until');
    }

    /** @test */
    public function batchStatusCannotBeInProcess()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->state('in-process')->create();

        $data = [
            'assign_until' => now()->addHour()->subMinute()->format('Y-m-d H:i'),
        ];

        $this->postJson(route('start-resell-batch', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('modification_forbidden');
    }

    /** @test */
    public function batchStatusCannotBeFinished()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->state('finished')->create();

        $data = [
            'assign_until' => now()->addHour()->subMinute()->format('Y-m-d H:i'),
        ];

        $this->postJson(route('start-resell-batch', $batch), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('modification_forbidden');
    }

    /** @test */
    public function itUpdatesBatchStatus()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create();
        $this->assertNotEquals(ResellBatch::IN_PROCESS, $batch->status);

        $data = [
            'assign_until' => now()->addHour()->addMinutes(2)->format('Y-m-d H:i'),
        ];

        $this->postJson(route('start-resell-batch', $batch), $data)
            ->assertStatus(202);

        $this->assertEquals(ResellBatch::IN_PROCESS, $batch->refresh()->status);
    }

    /** @test */
    public function itSavesAssignUntil()
    {
        Event::fake();

        $this->signIn(factory(User::class)->state('admin')->create());

        $batch = factory(ResellBatch::class)->create(['assign_until' => null]);
        $this->assertNull($batch->assign_until);

        $data = [
            'assign_until' => now()->addHour()->addMinutes(2)->format('Y-m-d H:i'),
        ];

        $this->postJson(route('start-resell-batch', $batch), $data)
            ->assertStatus(202);

        $this->assertEquals($data['assign_until'], $batch->refresh()->assign_until->format('Y-m-d H:i'));
    }
}
