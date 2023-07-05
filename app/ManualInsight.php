<?php

namespace App;

use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualInsight
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property string $account_id
 * @property string $campaign_id
 * @property int $impressions
 * @property int $clicks
 * @property string $spend
 * @property int $leads_cnt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\ManualAccount $account
 * @property-read \App\ManualCampaign $campaign
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 *
 * @method static Builder|ManualInsight allowedOffers()
 * @method static Builder|ManualInsight newModelQuery()
 * @method static Builder|ManualInsight newQuery()
 * @method static Builder|ManualInsight query()
 * @method static Builder|ManualInsight visible()
 * @method static Builder|ManualInsight whereAccountId($value)
 * @method static Builder|ManualInsight whereCampaignId($value)
 * @method static Builder|ManualInsight whereClicks($value)
 * @method static Builder|ManualInsight whereCreatedAt($value)
 * @method static Builder|ManualInsight whereDate($value)
 * @method static Builder|ManualInsight whereId($value)
 * @method static Builder|ManualInsight whereImpressions($value)
 * @method static Builder|ManualInsight whereLeadsCnt($value)
 * @method static Builder|ManualInsight whereSpend($value)
 * @method static Builder|ManualInsight whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualInsight extends Model
{
    use RecordEvents;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_insights';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast attributes to Carbon instances
     *
     * @var array
     */
    protected $dates = [
        'date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(ManualAccount::class, 'account_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(ManualCampaign::class, 'campaign_id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('manual_insights.date', '<', '2021-11-05 00:00:00');
        }

        if (! auth()->user()->isAdmin()) {
            $builder->whereHas('account', fn ($query) => $query->visible());
        }

        // if (auth()->id() === 89) {
        //     $builder->where('manual_insights.date', '>=', now()->subMonths(6)->toDateString());
        // }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowedOffers(Builder $builder)
    {
        if (auth()->user() instanceof User && !auth()->user()->isAdmin()) {
            $builder->whereHas('campaign.bundle', function ($query) {
                return $query->whereIn('manual_bundles.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
            });
        }

        return $builder;
    }
}
