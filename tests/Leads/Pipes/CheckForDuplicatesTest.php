<?php

namespace Leads\Pipes;

use App\Affiliate;
use App\Branch;
use App\Domain;
use App\Lead;
use App\Leads\Pipes\CheckForDuplicates;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckForDuplicatesTest extends TestCase
{
    use RefreshDatabase;

    protected CheckForDuplicates $pipe;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->pipe = new CheckForDuplicates();
    }

    /** @test */
    public function itChecksForDuplicatesUsingPhoneNumber()
    {
        $lead      = factory(Lead::class)->create();
        $duplicate = new Lead(['phone' => $lead->phone]);

        $this->expectException(ValidationException::class);
        $this->pipe->handle($duplicate, fn ($lead) => $this->assertTrue(false, 'Lead is passed through'));
    }

    /** @test */
    public function itChecksForDuplicatesUsingClickId()
    {
        $lead      = factory(Lead::class)->create(['clickid' => 'some-click-id']);
        $duplicate = new Lead(['clickid' => $lead->clickid]);

        $this->expectException(ValidationException::class);
        $this->pipe->handle($duplicate, fn ($lead) => $this->assertTrue(false, 'Lead is passed through'));
    }

    /** @test */
    public function itChecksForDuplicatesUsingClickIdOnlyWhenClickIsPresent()
    {
        $lead = factory(Lead::class)->create();

        $duplicate = new Lead([
            'phone'   => $lead->phone,
            'clickid' => $lead->clickid
        ]);

        // Sanity check
        $this->assertEmpty($duplicate->clickid, 'Click id somehow was set');
        $this->assertEquals($lead->clickid, $duplicate->clickid, 'Clicks doesnt match');

        $this->expectException(ValidationException::class);
        $this->pipe->handle($duplicate, fn ($lead) => $this->assertTrue(false, 'Lead is passed through'));
    }

    /** @test */
    public function itScopesQueryToTheSameLandingWhenLandingIsPresent()
    {
        $landing = factory(Domain::class)->state(Domain::LANDING)->create();
        $lead    = factory(Lead::class)->create(['landing_id' => $landing->id]);

        $this->expectException(ValidationException::class);
        $this->pipe->handle(
            new Lead(['phone' => $lead->phone, 'landing_id' => $landing->id]),
            fn ($lead) => $this->assertTrue(false, 'Lead is passed through')
        );
    }

    /** @test */
    public function itScopesQueryToTheSameAffiliateWhenAffiliateIsPresent()
    {
        $affiliate = factory(Affiliate::class)->create();
        $lead      = factory(Lead::class)->create(['affiliate_id' => $affiliate->id]);

        $this->expectException(ValidationException::class);
        $this->pipe->handle(
            new Lead(['phone' => $lead->phone, 'affiliate_id' => $affiliate->id]),
            fn ($lead) => $this->assertTrue(false, 'Lead is passed through')
        );
    }

    /** @test */
    public function itPassesUniqueLeadsFromSameLandingFurther()
    {
        $landing   = factory(Domain::class)->state(Domain::LANDING)->create();
        $lead      = factory(Lead::class)->create(['landing_id' => $landing->id]);
        $otherLead = factory(Lead::class)->make(['landing_id' => $landing->id]);

        // Sanity check
        $this->assertNotEquals($lead->phone, $otherLead->phone, 'Phone numbers match');

        $this->pipe->handle($otherLead, fn ($lead) => $this->assertNotNull($otherLead, 'Lead was not passed further'));
    }

    /** @test */
    public function itPassesUniqueLeadsFromSameAffiliateFurther()
    {
        $affiliate   = factory(Affiliate::class)->create();
        $lead        = factory(Lead::class)->create(['affiliate_id' => $affiliate->id]);
        $otherLead   = factory(Lead::class)->make(['affiliate_id' => $affiliate->id]);

        // Sanity check
        $this->assertNotEquals($lead->phone, $otherLead->phone, 'Phone numbers match');

        $this->pipe->handle($otherLead, fn ($lead) => $this->assertNotNull($otherLead, 'Lead was not passed further'));
    }



    /** @test */
    public function itPassesDuplicateUsingPhoneIfOfferBranch19()
    {
        $branch    = factory(Branch::class)->create(['id' => 19]);
        $offer     = factory(Offer::class)->create(['branch_id' => $branch->id]);
        $lead      = factory(Lead::class)->create(['offer_id' => $offer->id]);
        $duplicate = new Lead(['phone' => $lead->phone]);

        $this->pipe->handle($duplicate, fn ($lead) => $this->assertNotNull($duplicate, 'Lead was not passed further'));
    }

    /** @test */
    public function itChecksDuplicateUsingClickIdIfOfferBranch19()
    {
        $branch    = factory(Branch::class)->create(['id' => 19]);
        $offer     = factory(Offer::class)->create(['branch_id' => $branch->id]);
        $lead      = factory(Lead::class)->create(['offer_id' => $offer->id, 'clickid' => 'some-click-id']);
        $duplicate = new Lead(['phone' => $lead->phone, 'clickid' => $lead->clickid]);

        $this->expectException(ValidationException::class);
        $this->pipe->handle($duplicate, fn ($lead) => $this->assertTrue(false, 'Lead is passed through'));
    }

    /** @test */
    public function itPassesDuplicateUsingPhoneIfAffiliateBranch19()
    {
        $branch    = factory(Branch::class)->create(['id' => 19]);
        $affiliate = factory(Affiliate::class)->create(['branch_id' => $branch->id]);
        $lead      = factory(Lead::class)->create(['affiliate_id' => $affiliate->id]);
        $duplicate = new Lead(['phone' => $lead->phone]);

        $this->pipe->handle($duplicate, fn ($lead) => $this->assertNotNull($duplicate, 'Lead was not passed further'));
    }

    /** @test */
    public function itChecksDuplicateUsingClickIdIfAffiliateBranch19()
    {
        $branch    = factory(Branch::class)->create(['id' => 19]);
        $affiliate = factory(Affiliate::class)->create(['branch_id' => $branch->id]);
        $lead      = factory(Lead::class)->create(['affiliate_id' => $affiliate->id, 'clickid' => 'some-click-id']);
        $duplicate = new Lead(['phone' => $lead->phone, 'clickid' => $lead->clickid]);

        $this->expectException(ValidationException::class);
        $this->pipe->handle($duplicate, fn ($lead) => $this->assertTrue(false, 'Lead is passed through'));
    }
}
