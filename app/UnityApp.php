<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UnityApp
 *
 * @property string $id
 * @property string $name
 * @property string $store
 * @property string|null $store_id
 * @property string|null $adomain
 * @property int $organization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UnityCampaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UnityInsight[] $insights
 * @property-read int|null $insights_count
 * @property-read \App\UnityOrganization $organization
 *
 * @method static Builder|UnityApp newModelQuery()
 * @method static Builder|UnityApp newQuery()
 * @method static Builder|UnityApp query()
 * @method static Builder|UnityApp visible()
 * @method static Builder|UnityApp whereAdomain($value)
 * @method static Builder|UnityApp whereCreatedAt($value)
 * @method static Builder|UnityApp whereId($value)
 * @method static Builder|UnityApp whereName($value)
 * @method static Builder|UnityApp whereOrganizationId($value)
 * @method static Builder|UnityApp whereStore($value)
 * @method static Builder|UnityApp whereStoreId($value)
 * @method static Builder|UnityApp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UnityApp extends Model
{
    public const STORE_APPLE              = 'apple';
    public const STORE_GOOGLE             = 'google';
    public const STORE_STANDALONE_ANDROID = 'standalone_android';

    public const STORES = [
        self::STORE_APPLE,
        self::STORE_GOOGLE,
        self::STORE_STANDALONE_ANDROID,
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'unity_apps';

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
    public function organization()
    {
        return $this->belongsTo(UnityOrganization::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(UnityCampaign::class, 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function insights()
    {
        return $this->hasMany(UnityInsight::class, 'app_id');
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
