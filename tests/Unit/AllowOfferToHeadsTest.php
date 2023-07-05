<?php

namespace Tests\Unit;

use App\Branch;
use App\Events\OfferAllowed;
use App\Listeners\AllowOfferToHeads;
use App\Offer;
use App\Team;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AllowOfferToHeadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider notAutoAllowedRoles
     *
     * @param mixed $role
     */
    public function itDoesNotAllowOfferToTeamleadIfRole($role)
    {
        Event::fake();
        $user             = factory(User::class)->create(['role' => $role]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::TEAMLEAD]);
        $offer            = factory(Offer::class)->create();
        $team             = factory(Team::class)->create();
        $team->users()->attach($user);
        $team->users()->attach($autoAllowedUser);

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    /**
     * @test
     * @dataProvider notAutoAllowedRoles
     *
     * @param mixed $role
     */
    public function itDoesNotAllowOfferToHeadIfRole($role)
    {
        Event::fake();
        $branch           = factory(Branch::class)->create();
        $user             = factory(User::class)->create(['role' => $role, 'branch_id' => $branch->id]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::HEAD, 'branch_id' => $branch->id]);
        $offer            = factory(Offer::class)->create();

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    public function notAutoAllowedRoles()
    {
        return collect(User::ROLES)
            ->reject(User::BUYER)
            ->mapWithKeys(fn ($role, $i) => [$role => [$role]])
            ->toArray();
        /*return collect(User::ROLES)->reject(User::BUYER)->map(fn ($role) => [$role])->toArray();*/
    }

    /** @test */
    public function itDoesNotAllowOfferIfBothInBranchAndNotTeammates()
    {
        Event::fake();
        $branch           = factory(Branch::class)->create();
        $user             = factory(User::class)->create(['role' => User::BUYER, 'branch_id' => $branch->id]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::TEAMLEAD, 'branch_id' => $branch->id]);
        $offer            = factory(Offer::class)->create();

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itDoesNotAllowOfferIfTeammatesAndNotInTheSameBranch()
    {
        Event::fake();
        $user             = factory(User::class)->create([
            'role'      => User::BUYER,
            'branch_id' => factory(Branch::class)->create()->id,
        ]);
        $autoAllowedUser  = factory(User::class)->create([
            'role'      => User::HEAD,
            'branch_id' => factory(Branch::class)->create()->id,
        ]);
        $offer            = factory(Offer::class)->create();
        $team             = factory(Team::class)->create();
        $team->users()->attach($user);
        $team->users()->attach($autoAllowedUser);

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itDoesNotAllowOfferIfTeammatesAndNoBranch()
    {
        Event::fake();
        $user             = factory(User::class)->create(['role' => User::BUYER, 'branch_id' => null]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::HEAD, 'branch_id' => null]);
        $offer            = factory(Offer::class)->create();
        $team             = factory(Team::class)->create();
        $team->users()->attach($user);
        $team->users()->attach($autoAllowedUser);

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itDoesNotAllowOfferIfNotTeammates()
    {
        Event::fake();
        $user            = factory(User::class)->create(['role' => User::BUYER]);
        $autoAllowedUser = factory(User::class)->create(['role' => User::TEAMLEAD]);
        $offer           = factory(Offer::class)->create();
        factory(Team::class)->create()->users()->attach($user);
        factory(Team::class)->create()->users()->attach($autoAllowedUser);

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itDoesNotAllowOfferIfNotInTheSameBranch()
    {
        Event::fake();
        $user             = factory(User::class)->create([
            'role'      => User::BUYER,
            'branch_id' => factory(Branch::class)->create()->id,
        ]);
        $autoAllowedUser  = factory(User::class)->create([
            'role'      => User::HEAD,
            'branch_id' => factory(Branch::class)->create()->id,
        ]);
        $offer           = factory(Offer::class)->create();

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(0, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itDoesNothingIfOfferIsAlreadyAllowed()
    {
        Event::fake();
        $user             = factory(User::class)->create(['role' => User::BUYER]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::TEAMLEAD]);
        $offer            = factory(Offer::class)->create();
        $team             = factory(Team::class)->create();
        $team->users()->attach($user);
        $team->users()->attach($autoAllowedUser);
        $autoAllowedUser->allowedOffers()->attach($offer);
        $this->assertCount(1, $autoAllowedUser->allowedOffers);

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(1, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itAllowsOfferToTeamleadIfTeammates()
    {
        Event::fake();
        $user             = factory(User::class)->create(['role' => User::BUYER]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::TEAMLEAD]);
        $offer            = factory(Offer::class)->create();
        $team             = factory(Team::class)->create();
        $team->users()->attach($user);
        $team->users()->attach($autoAllowedUser);

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(1, $autoAllowedUser->allowedOffers);
    }

    /** @test */
    public function itAllowsOfferToHeadIfInTheSameBranch()
    {
        Event::fake();
        $branch           = factory(Branch::class)->create();
        $user             = factory(User::class)->create(['role' => User::BUYER, 'branch_id' => $branch->id]);
        $autoAllowedUser  = factory(User::class)->create(['role' => User::HEAD, 'branch_id' => $branch->id]);
        $offer            = factory(Offer::class)->create();

        (new AllowOfferToHeads())->handle(new OfferAllowed($offer, $user));

        $this->assertCount(1, $autoAllowedUser->allowedOffers);
        Event::assertNotDispatched(OfferAllowed::class);
    }
}
