<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UnityInsight
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property string $app_id
 * @property string $campaign_id
 * @property int $views
 * @property int $clicks
 * @property string $spend
 * @property int $installs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\UnityApp $app
 * @property-read \App\UnityCampaign $campaign
 * @property-read \App\UnityOrganization|null $organization
 *
 * @method static Builder|UnityInsight newModelQuery()
 * @method static Builder|UnityInsight newQuery()
 * @method static Builder|UnityInsight query()
 * @method static Builder|UnityInsight visible()
 * @method static Builder|UnityInsight whereAppId($value)
 * @method static Builder|UnityInsight whereCampaignId($value)
 * @method static Builder|UnityInsight whereClicks($value)
 * @method static Builder|UnityInsight whereCreatedAt($value)
 * @method static Builder|UnityInsight whereDate($value)
 * @method static Builder|UnityInsight whereId($value)
 * @method static Builder|UnityInsight whereInstalls($value)
 * @method static Builder|UnityInsight whereSpend($value)
 * @method static Builder|UnityInsight whereUpdatedAt($value)
 * @method static Builder|UnityInsight whereViews($value)
 * @mixin \Eloquent
 */
class UnityInsight extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'unity_insights';

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
    public function app()
    {
        return $this->belongsTo(UnityApp::class, 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(UnityCampaign::class, 'campaign_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function organization()
    {
        return $this->hasOneThrough(
            UnityOrganization::class,
            UnityApp::class,
            'id',
            'id',
            'app_id',
            'organization_id'
        );
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        return $builder;
    }
}
