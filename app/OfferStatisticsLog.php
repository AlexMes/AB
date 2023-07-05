<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OfferStatisticsLog
 *
 * @property int $id
 * @property string $datetime
 * @property int $offer_id
 * @property int|null $impressions
 * @property int|null $clicks
 * @property int|null $accounts
 * @property string|null $cpm
 * @property string|null $cpc
 * @property string|null $ctr
 * @property string|null $cr
 * @property int|null $fb_leads
 * @property string|null $fb_cpl
 * @property int|null $binom_clicks
 * @property int|null $binom_unique_clicks
 * @property int|null $lp_views
 * @property int|null $lp_clicks
 * @property string|null $lp_ctr
 * @property int|null $binom_leads
 * @property string|null $binom_cpl
 * @property int|null $affiliate_valid_leads
 * @property int|null $affiliate_leads
 * @property int|null $leads
 * @property int|null $valid_leads
 * @property string|null $offer_cr
 * @property string|null $cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Offer $offer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereAccounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereAffiliateLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereAffiliateValidLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereBinomClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereBinomCpl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereBinomLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereBinomUniqueClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereCpc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereCpm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereCr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereCtr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereFbCpl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereFbLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereLpClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereLpCtr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereLpViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereOfferCr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfferStatisticsLog whereValidLeads($value)
 * @mixin \Eloquent
 */
class OfferStatisticsLog extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'offer_statistics_log';

    /**
     * Guarded model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get related results
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
