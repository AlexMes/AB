<?php

namespace App;

use App\Events\AffiliateCreating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Affiliate
 *
 * @property int $id
 * @property string $name
 * @property int|null $offer_id
 * @property string $api_key
 * @property string $cpl
 * @property string $cpa
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $traffic_source_id
 * @property int|null $branch_id
 * @property string|null $postback
 * @property-read \App\Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Deposit[] $deposits
 * @property-read int|null $deposits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \App\Offer|null $offer
 * @property-read \App\TrafficSource|null $trafficSource
 *
 * @method static Builder|Affiliate newModelQuery()
 * @method static Builder|Affiliate newQuery()
 * @method static Builder|Affiliate query()
 * @method static Builder|Affiliate visible()
 * @method static Builder|Affiliate whereApiKey($value)
 * @method static Builder|Affiliate whereBranchId($value)
 * @method static Builder|Affiliate whereCpa($value)
 * @method static Builder|Affiliate whereCpl($value)
 * @method static Builder|Affiliate whereCreatedAt($value)
 * @method static Builder|Affiliate whereId($value)
 * @method static Builder|Affiliate whereName($value)
 * @method static Builder|Affiliate whereOfferId($value)
 * @method static Builder|Affiliate wherePostback($value)
 * @method static Builder|Affiliate whereTrafficSourceId($value)
 * @method static Builder|Affiliate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Affiliate extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $hidden = ['api_key'];

    /**
     * @var string[]
     */
    protected $dispatchesEvents = [
        'creating' => AffiliateCreating::class,
    ];

    /**
     *  Deposits from affiliate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function deposits()
    {
        return $this->hasManyThrough(Deposit::class, Lead::class);
    }

    /**
     * Leads from affiliate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Affiliate's offer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }


    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->hasRole([User::HEAD, User::SUPPORT])) {
            return $builder->where('branch_id', auth()->user()->branch_id);
        }

        return $builder;
    }
}
