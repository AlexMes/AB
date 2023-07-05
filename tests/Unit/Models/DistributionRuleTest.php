<?php

namespace Tests\Unit\Models;

use App\DistributionRule;
use App\Offer;
use App\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class DistributionRuleTest extends TestCase
{
    use RefreshDatabase;

    public const COUNTRY_AR = 'AR';
    public const COUNTRY_AU = 'AU';

    protected Office $office;
    protected Offer $offer1;
    protected Offer $offer2;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Bus::fake();

        $this->office = factory(Office::class)->create();
        $this->offer1 = factory(Offer::class)->create();
        $this->offer2 = factory(Offer::class)->create();
    }

    // office allowed
    /** @test */
    public function itAllowsOnlyOfficeByCountry()
    {
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }

    /** @test */
    public function itAllowsAllExceptDeniedOfficeByCountry()
    {
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }

    /** @test */
    public function itAllowsOnlyCountryInOfferContext()
    {
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // allowed country in offer context
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        // another offer must be allowed anyway
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }

    /** @test */
    public function itAllowsAllExceptDeniedCountryInOfferContext()
    {
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // allowed country in offer context
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        // another offer must be allowed anyway
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }


    // office allowed + offer denied
    /** @test */
    public function itAllowsOnlyOfficeByCountryExceptOfferDenied()
    {
        // allow all for country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        // other countries are denied for any offer
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }

    /** @test */
    public function itAllowsAnotherOfferWhenOnlyOfficeByCountryExceptOfferDenied()
    {
        // allow all for country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // offer2 is allowed in another country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer2->id,
            'geo'           => self::COUNTRY_AU,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
    }

    /** @test */
    public function itDeniesAnotherOfferWhenOnlyOfficeByCountryExceptOfferDenied()
    {
        // allow all for country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // offer2 is denied in another country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer2->id,
            'geo'           => self::COUNTRY_AU,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
    }


    // office denied + offer allowed
    /** @test */
    public function itAllowsAllExceptDeniedOfficeByCountryExceptOfferAllowed()
    {
        // deny all for country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        // other countries are allowed for other offers, except offer1
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }

    /** @test */
    public function itDeniesAnotherOfferWhenDeniedOfficeByCountryExceptOfferAllowed()
    {
        // deny all for country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // offer2 is denied in another country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer2->id,
            'geo'           => self::COUNTRY_AU,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
    }

    /** @test */
    public function itAllowsAnotherOfferWhenDeniedOfficeByCountryExceptOfferAllowed()
    {
        // deny all for country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // offer2 is allowed in another country
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer2->id,
            'geo'           => self::COUNTRY_AU,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
    }


    // global denied
    /** @test */
    public function itDeniesGloballyByCountry()
    {
        DistributionRule::create([
            'office_id'     => null,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }

    /** @test */
    public function itDeniesGloballyByCountryInOfferContext()
    {
        DistributionRule::create([
            'office_id'     => null,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }


    // global denied + office allowed
    /** @test */
    public function itDeniesGloballyByCountryExceptAllowedOfficeByCountry()
    {
        DistributionRule::create([
            'office_id'     => null,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        // we allowed the only country for office, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }

    /** @test */
    public function itDeniesGloballyByCountryInOfferContextExceptAllowedOfficeByCountry()
    {
        DistributionRule::create([
            'office_id'     => null,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        // we allowed the only country for office, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }


    // global denied + offer allowed
    /** @test */
    public function itDeniesGloballyByCountryExceptOfferAllowed()
    {
        DistributionRule::create([
            'office_id'     => null,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
        // we allowed the only country for offer, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }

    /** @test */
    public function itDeniesGloballyByCountryInOfferContextExceptOfferAllowed()
    {
        DistributionRule::create([
            'office_id'     => null,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
        // we allowed the only country for offer, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }


    // global denied + office allowed + offer denied
    /** @test */
    public function itDeniesGloballyByCountryExceptAllowedOfficeByCountryExceptOfferDenied()
    {
        DistributionRule::create([
            'office_id'     => null,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        // we allowed the only country for office, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }

    /** @test */
    public function itDeniesGloballyByCountryInOfferContextExceptAllowedOfficeByCountryExceptOfferDenied()
    {
        DistributionRule::create([
            'office_id'     => null,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        // we allowed the only country for office, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
    }


    // global denied + office denied + offer allowed
    /** @test */
    public function itDeniesGloballyByCountryWithDeniedOfficeByCountryExceptOfferAllowed()
    {
        DistributionRule::create([
            'office_id'     => null,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
        // we allowed the only country for offer, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }

    /** @test */
    public function itDeniesGloballyByCountryInOfferContextWithDeniedOfficeByCountryExceptOfferAllowed()
    {
        DistributionRule::create([
            'office_id'     => null,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        DistributionRule::create([
            'office_id'     => $this->office->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => false,
        ]);

        // except offer1
        DistributionRule::create([
            'office_id'     => $this->office->id,
            'offer_id'      => $this->offer1->id,
            'geo'           => self::COUNTRY_AR,
            'country_name'  => Str::random(),
            'allowed'       => true,
        ]);

        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer1->id));
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AR, $this->offer2->id));
        $this->assertCount(0, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer2->id));
        // we allowed the only country for offer, so other are denied
        $this->assertCount(1, DistributionRule::getDeniedOffices(self::COUNTRY_AU, $this->offer1->id));
    }
}
