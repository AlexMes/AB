<?php

namespace Tests\Deluge;

use App\Deluge\Jobs\ProcessInsight;
use App\ManualAccount;
use App\ManualBundle;
use App\ManualCampaign;
use App\ManualInsight;
use App\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProcessInsightTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    protected array $attributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            'date_start'        => now()->toDateString(),
            'date_end'          => now()->toDateString(),
            'account_id'        => '37819273829182378',
            'campaign_id'       => '83748198478920394',
            'campaign_name'     => 'something-campaign-utm_campaign-key-gg',
            'impressions'       => 512,
            'clicks'            => 312,
            'spend'             => 44,
            'leads_cnt'         => 27,
        ];
    }

    /** @test */
    public function itCreatesInsight()
    {
        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);
        factory(ManualCampaign::class)->create([
            'name'        => $this->attributes['campaign_name'],
            'account_id'  => $this->attributes['account_id'],
            'id'          => $this->attributes['campaign_id'],
        ]);

        ProcessInsight::dispatchNow($this->attributes);

        $this->assertDatabaseHas(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }

    /** @test */
    public function itUpdatesInsight()
    {
        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);
        factory(ManualCampaign::class)->create([
            'name'        => $this->attributes['campaign_name'],
            'account_id'  => $this->attributes['account_id'],
            'id'          => $this->attributes['campaign_id'],
        ]);
        factory(ManualInsight::class)->create([
            'date'                 => $this->attributes['date_end'],
            'account_id'           => $this->attributes['account_id'],
            'campaign_id'          => $this->attributes['campaign_id'],
        ]);

        ProcessInsight::dispatchNow($this->attributes);

        $this->assertDatabaseHas(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }

    /** @test */
    public function itResolveCampaignIfDoesntExist()
    {
        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);
        factory(ManualBundle::class)->create();

        ProcessInsight::dispatchNow($this->attributes);

        $this->assertDatabaseHas(
            'manual_campaigns',
            [
                'id'         => $this->attributes['campaign_id'],
                'name'       => $this->attributes['campaign_name'],
                'account_id' => $this->attributes['account_id'],
            ]
        );

        $this->assertDatabaseHas(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }

    /** @test */
    public function itResolveBundleIfExists()
    {
        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);

        $utmKey = trim(Str::afterLast($this->attributes['campaign_name'], 'campaign-'));
        factory(ManualBundle::class)->create();
        $bundle = factory(ManualBundle::class)->create(['name' => $utmKey]);

        ProcessInsight::dispatchNow($this->attributes);

        $this->assertDatabaseHas(
            'manual_campaigns',
            [
                'id'         => $this->attributes['campaign_id'],
                'name'       => $this->attributes['campaign_name'],
                'account_id' => $this->attributes['account_id'],
                'bundle_id'  => $bundle->id,
            ]
        );

        $this->assertDatabaseHas(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }

    /** @test */
    public function itResolvesBundleIfDoesntExist()
    {
        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);

        $utmKey = trim(Str::afterLast($this->attributes['campaign_name'], 'campaign-'));
        $offer  = factory(Offer::class)->create();
        factory(ManualBundle::class)->create();

        ProcessInsight::dispatchNow($this->attributes);

        $this->assertDatabaseHas(
            'manual_campaigns',
            [
                'id'         => $this->attributes['campaign_id'],
                'name'       => $this->attributes['campaign_name'],
                'account_id' => $this->attributes['account_id'],
                'bundle_id'  => optional(ManualBundle::whereName($utmKey)->whereOfferId($offer->id)->first())->id,
            ]
        );

        $this->assertDatabaseHas(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }

    /** @test */
    public function itThrowsExceptionIfNoDefaultOffer()
    {
        $this->expectExceptionMessage('Could not resolve bundle');

        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);

        ProcessInsight::dispatchNow($this->attributes);
    }

    /** @test */
    public function itSkipsInsightIfNoAcc()
    {
        factory(Offer::class)->create();

        ProcessInsight::dispatchNow($this->attributes);

        $this->assertDatabaseMissing(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }

    /** @test */
    public function itSkipsInsightIfDatePeriodGreaterThanOneDay()
    {
        factory(ManualAccount::class)->create(['account_id' => $this->attributes['account_id']]);
        factory(Offer::class)->create();

        ProcessInsight::dispatchNow(array_merge($this->attributes, ['date_start' => now()->subDay()->toDateString()]));

        $this->assertDatabaseMissing(
            'manual_insights',
            array_merge(
                Arr::except($this->attributes, ['campaign_name', 'date_start', 'date_end']),
                ['date' => Carbon::parse($this->attributes['date_end'])],
            ),
        );
    }
}
