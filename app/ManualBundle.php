<?php

namespace App;

use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualBundle
 *
 * @property int $id
 * @property string $name
 * @property int $offer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $traffic_source_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualCampaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualInsight[] $insights
 * @property-read int|null $insights_count
 * @property-read \App\Offer $offer
 * @property-read \App\ManualTrafficSource|null $trafficSource
 *
 * @method static Builder|ManualBundle allowedOffers()
 * @method static Builder|ManualBundle newModelQuery()
 * @method static Builder|ManualBundle newQuery()
 * @method static Builder|ManualBundle query()
 * @method static Builder|ManualBundle whereCreatedAt($value)
 * @method static Builder|ManualBundle whereId($value)
 * @method static Builder|ManualBundle whereName($value)
 * @method static Builder|ManualBundle whereOfferId($value)
 * @method static Builder|ManualBundle whereTrafficSourceId($value)
 * @method static Builder|ManualBundle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualBundle extends Model
{
    use RecordEvents;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_bundles';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trafficSource()
    {
        return $this->belongsTo(ManualTrafficSource::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('manual_bundles.created_at', '<', '2021-11-05 00:00:00');
        }

        if (
            auth()->user() instanceof User && !auth()->user()->isAdmin()
            && !auth()->user()->isDeveloper()
        ) {
            $builder->whereIn('manual_bundles.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
        }

        return $builder;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(ManualCampaign::class, 'bundle_id');
    }

    public function insights()
    {
        return $this->hasManyThrough(ManualInsight::class, ManualCampaign::class, 'bundle_id', 'campaign_id');
    }
}
