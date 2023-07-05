<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UnityCampaign
 *
 * @property string $id
 * @property string $name
 * @property string $goal
 * @property bool $enabled
 * @property string $app_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\UnityApp $app
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UnityInsight[] $insights
 * @property-read int|null $insights_count
 * @property-read \App\UnityOrganization|null $organization
 *
 * @method static Builder|UnityCampaign newModelQuery()
 * @method static Builder|UnityCampaign newQuery()
 * @method static Builder|UnityCampaign query()
 * @method static Builder|UnityCampaign visible()
 * @method static Builder|UnityCampaign whereAppId($value)
 * @method static Builder|UnityCampaign whereCreatedAt($value)
 * @method static Builder|UnityCampaign whereEnabled($value)
 * @method static Builder|UnityCampaign whereGoal($value)
 * @method static Builder|UnityCampaign whereId($value)
 * @method static Builder|UnityCampaign whereName($value)
 * @method static Builder|UnityCampaign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UnityCampaign extends Model
{
    public const GOAL_INSTALLS  = 'installs';
    public const GOAL_ROAS      = 'roas';
    public const GOAL_RETENTION = 'retention';

    public const GOALS = [
        self::GOAL_INSTALLS,
        self::GOAL_ROAS,
        self::GOAL_RETENTION,
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'unity_campaigns';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Disable incrementing primary key
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {
        return $this->belongsTo(UnityApp::class, 'app_id');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function insights()
    {
        return $this->hasMany(UnityInsight::class, 'campaign_id');
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
