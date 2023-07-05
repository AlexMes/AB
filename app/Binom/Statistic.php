<?php

namespace App\Binom;

use App\Offer;
use App\Traits\HasVisibilityScope;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * App\Binom\Statistic
 *
 * @property string $date
 * @property int $campaign_id
 * @property int $clicks
 * @property int $lp_clicks
 * @property int $lp_views
 * @property int $unique_clicks
 * @property int $unique_camp_clicks
 * @property int $leads
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $utm_source
 * @property string|null $utm_campaign
 * @property string|null $fb_campaign_id
 * @property string|null $account_id
 * @property int|null $user_id
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $fb_adset_id
 * @property string|null $fb_ad_id
 * @property int|null $binom_id
 * @property string|null $cost
 *
 * @method static Builder|Statistic allowedOffers()
 * @method static Builder|Statistic forOffer(\App\Offer $offer)
 * @method static Builder|Statistic forOffers($offers)
 * @method static Builder|Statistic newModelQuery()
 * @method static Builder|Statistic newQuery()
 * @method static Builder|Statistic query()
 * @method static Builder|Statistic whereAccountId($value)
 * @method static Builder|Statistic whereBinomId($value)
 * @method static Builder|Statistic whereCampaignId($value)
 * @method static Builder|Statistic whereClicks($value)
 * @method static Builder|Statistic whereCost($value)
 * @method static Builder|Statistic whereCreatedAt($value)
 * @method static Builder|Statistic whereDate($value)
 * @method static Builder|Statistic whereFbAdId($value)
 * @method static Builder|Statistic whereFbAdsetId($value)
 * @method static Builder|Statistic whereFbCampaignId($value)
 * @method static Builder|Statistic whereLeads($value)
 * @method static Builder|Statistic whereLpClicks($value)
 * @method static Builder|Statistic whereLpViews($value)
 * @method static Builder|Statistic whereUniqueCampClicks($value)
 * @method static Builder|Statistic whereUniqueClicks($value)
 * @method static Builder|Statistic whereUpdatedAt($value)
 * @method static Builder|Statistic whereUserId($value)
 * @method static Builder|Statistic whereUtmCampaign($value)
 * @method static Builder|Statistic whereUtmContent($value)
 * @method static Builder|Statistic whereUtmSource($value)
 * @method static Builder|Statistic whereUtmTerm($value)
 * @mixin \Eloquent
 */
class Statistic extends Model
{
    use HasVisibilityScope;

    /**
     * @var string
     */
    protected $table = 'binom_statistics';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * No incrementing whatever.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * No primary key
     *
     * @var null
     */
    protected $primaryKey = null;

    /**
     * Get statistic tied to offer
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \App\Offer                            $offer
     */
    public function scopeforOffer(Builder $builder, Offer $offer)
    {
        $builder->whereIn('campaign_id', Campaign::whereOfferId($offer->id)->pluck('id')->values());
    }

    /**
     * Get statistic tied to offer
     *
     * @param \Illuminate\Database\Eloquent\Builder      $builder
     * @param \Illuminate\Support\Collection|array|Offer $offers
     */
    public function scopeforOffers(Builder $builder, $offers)
    {
        $ofrs = $offers;
        if ($offers instanceof Collection) {
            $ofrs = $offers->pluck('id')->values();
        }
        if (is_array($ofrs) && array_key_exists('id', $ofrs)) {
            $ofrs = Arr::pluck($offers, 'id');
        }
        if ($offers instanceof Offer) {
            $ofrs = [$offers->id];
        }

        $builder->whereIn(
            'campaign_id',
            Campaign::whereNotNull('offer_id')->when($ofrs, function (Builder $query) use ($ofrs) {
                $query->whereIn('offer_id', $ofrs);
            })->pluck('id')->values()
        );
    }

    /**
     * Limit value to 255 characters
     *
     * @param $value
     *
     * @return void
     */
    public function setUtmSourceAttribute($value)
    {
        $this->attributes['utm_source'] = substr($value, 0, 250);
    }


    /**
     * Limit value to 255 characters
     *
     * @param $value
     *
     * @return void
     */
    public function setUtmTermAttribute($value)
    {
        $this->attributes['utm_term'] = substr($value, 0, 250);
    }


    /**
     * Limit value to 255 characters
     *
     * @param $value
     *
     * @return void
     */
    public function setUtmContentAttribute($value)
    {
        $this->attributes['utm_content'] = substr($value, 0, 250);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (
            auth()->user() instanceof User &&
            !auth()->user()->isAdmin() && !auth()->user()->isSupport()
        ) {
            $builder->whereIn(
                'binom_statistics.campaign_id',
                Campaign::whereIn('offer_id', auth()->user()->allowedOffers->pluck('id')->values())
                    ->pluck('id')
                    ->values()
            );
        }

        return $builder;
    }
}
