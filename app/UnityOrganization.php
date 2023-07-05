<?php

namespace App;

use App\Deluge\Events\Unity\Organizations\Created;
use App\Facebook\Traits\HasIssues;
use App\Unity\Jobs\CollectApps;
use App\Unity\Jobs\CollectCampaigns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UnityOrganization
 *
 * @property int $id
 * @property string $name
 * @property string $organization_core_id
 * @property string $organization_id
 * @property string $api_key
 * @property string $key_id
 * @property string $secret_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UnityApp[] $apps
 * @property-read int|null $apps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UnityCampaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read bool $has_issues
 * @property-read string|null $last_issue
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UnityInsight[] $insights
 * @property-read int|null $insights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Issue[] $issues
 * @property-read int|null $issues_count
 *
 * @method static Builder|UnityOrganization newModelQuery()
 * @method static Builder|UnityOrganization newQuery()
 * @method static Builder|UnityOrganization query()
 * @method static Builder|UnityOrganization visible()
 * @method static Builder|UnityOrganization whereApiKey($value)
 * @method static Builder|UnityOrganization whereCreatedAt($value)
 * @method static Builder|UnityOrganization whereId($value)
 * @method static Builder|UnityOrganization whereKeyId($value)
 * @method static Builder|UnityOrganization whereName($value)
 * @method static Builder|UnityOrganization whereOrganizationCoreId($value)
 * @method static Builder|UnityOrganization whereOrganizationId($value)
 * @method static Builder|UnityOrganization whereSecretKey($value)
 * @method static Builder|UnityOrganization whereUpdatedAt($value)
 * @method static Builder|UnityOrganization withoutIssues()
 * @mixin \Eloquent
 */
class UnityOrganization extends Model
{
    use HasIssues;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'unity_organizations';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'api_key',
        'key_id',
        'secret_key',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Created::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apps()
    {
        return $this->hasMany(UnityApp::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function campaigns()
    {
        return $this->hasManyThrough(
            UnityCampaign::class,
            UnityApp::class,
            'organization_id',
            'app_id',
            'id',
            'id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function insights()
    {
        return $this->hasManyThrough(
            UnityInsight::class,
            UnityApp::class,
            'organization_id',
            'app_id',
            'id',
            'id'
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

    /**
     * @return \App\Unity\UnityApp
     */
    public function initUnityApp(): \App\Unity\UnityApp
    {
        return app(\App\Unity\UnityApp::class)
            ->useApiKey($this->api_key)
            ->useCredentials($this->key_id, $this->secret_key);
    }

    /**
     * @param bool $force
     *
     * @return void
     */
    public function refreshUnityData($force = false)
    {
        CollectApps::withChain([
            new CollectCampaigns($this, $force),
        ])->dispatch($this, $force)->allOnQueue(AdsBoard::QUEUE_UNITY);
    }

    /**
     * @return bool
     */
    public function hasNotToken(): bool
    {
        return false;
    }
}
