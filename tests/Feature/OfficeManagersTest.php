<?php

namespace Tests\Feature;

use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\Manager;
use App\Offer;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OfficeManagersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function deleteRouteRequiresAuth()
    {
        Event::fake();

        $this->assertGuest();

        $manager = factory(Manager::class)->create();

        $this->getJson(route('offices.managers.destroy', ['office' => $manager->office, 'manager' => $manager]))
            ->assertStatus(401);
    }

    /** @test */
    public function assignTypeIsRequiredOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'   => $manager->office,
                'manager'  => $manager,
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('assign_type');
    }

    /** @test */
    public function assignTypeCannotBeAnythingOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $assignManager = factory(Manager::class)->create(['office_id' => $manager->office_id]);

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'managers'    => [$assignManager->id],
                'assign_type' => 'anything',
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('assign_type');
    }

    /** @test */
    public function assignManagersShouldExistOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'   => $manager->office,
                'manager'  => $manager,
                'managers' => [100500],
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('managers.0');
    }

    /** @test */
    public function assignManagersShouldBeArrayOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $assignManager = factory(Manager::class)->create(['office_id' => $manager->office_id]);

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'   => $manager->office,
                'manager'  => $manager,
                'managers' => $assignManager->id
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('managers');
    }

    /** @test */
    public function assignManagersNotContainDeletingOneOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'   => $manager->office,
                'manager'  => $manager,
                'managers' => [$manager->id]
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('managers.0');
    }

    /** @test */
    public function assignManagersShouldBeFromTheSameOfficeOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $assignManager = factory(Manager::class)->create();

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'managers'    => [$assignManager->id],
                'assign_type' => 'onlyManagers',
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('managers.0');
    }

    /** @test */
    public function assignManagersAreRequiredIfTypeIsOnlyManagersOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'assign_type' => 'onlyManagers',
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('managers');
    }

    /** @test */
    public function assignManagersAreNotRequiredIfTypeIsExcludeManagersOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        factory(Manager::class)->create(['office_id' => $manager->office_id]);

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'managers'    => [],
                'assign_type' => 'excludeManagers',
            ]
        ))->assertStatus(204);
    }

    /** @test */
    public function canNotExcludeAllManagersOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $assignManagers = factory(Manager::class, 3)->create(['office_id' => $manager->office_id]);

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'managers'    => $assignManagers->pluck('id')->toArray(),
                'assign_type' => 'excludeManagers',
            ]
        ))
            ->assertStatus(422)
            ->assertJsonValidationErrors('managers');
    }

    /** @test */
    public function itDeletesManagerOnDelete()
    {
        Event::fake();

        $this->signIn();

        $manager = factory(Manager::class)->create();

        $assignManager = factory(Manager::class)->create(['office_id' => $manager->office_id]);

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'managers'    => [$assignManager->id],
                'assign_type' => 'onlyManagers',
            ]
        ))->assertStatus(204);

        $this->assertDatabaseMissing('managers', ['id' => $manager->id, 'deleted_at' => null]);
    }

    /** @test */
    public function itReassignsLeadsProperlyOnDelete()
    {
        Event::fake();

        $this->signIn();

        $office = factory(Office::class)->create();

        $offer = factory(Offer::class)->create();

        $manager = factory(Manager::class)->create(['office_id' => $office->id]);

        $assignManager1 = factory(Manager::class)->create(['office_id' => $office->id]);
        $assignManager2 = factory(Manager::class)->create(['office_id' => $office->id]);

        $order = \App\LeadsOrder::create([
            'office_id' => $office->id,
            'date'      => now(),
        ]);

        $route = factory(LeadOrderRoute::class)->create([
            'order_id'   => $order->id,
            'manager_id' => $manager->id,
            'offer_id'   => $offer->id,
        ]);

        factory(LeadOrderAssignment::class, 4)->create(['route_id' => $route->id]);

        $this->deleteJson(route(
            'offices.managers.destroy',
            [
                'office'      => $manager->office,
                'manager'     => $manager,
                'managers'    => [],
                'assign_type' => 'excludeManagers',
            ]
        ))->assertStatus(204);

        $this->assertCount(0, $manager->assignments);

        $this->assertCount(2, $assignManager1->assignments);
        $this->assertCount(2, $assignManager2->assignments);
    }
}
