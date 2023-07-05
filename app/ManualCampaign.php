<?php

namespace App;

use App\Deluge\Events\Campaigns\Created;
use App\Deluge\Events\Campaigns\Saving;
use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualCampaign
 *
 * @property string $id
 * @property string $name
 * @property string $account_id
 * @property int $bundle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $utm_key
 * @property string|null $creo
 * @property-read \App\ManualAccount $account
 * @property-read \App\ManualBundle $bundle
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualInsight[] $insights
 * @property-read int|null $insights_count
 * @property-read \App\Offer|null $offer
 * @property-read \App\User|null $user
 *
 * @method static Builder|ManualCampaign allowedOffers()
 * @method static Builder|ManualCampaign newModelQuery()
 * @method static Builder|ManualCampaign newQuery()
 * @method static Builder|ManualCampaign query()
 * @method static Builder|ManualCampaign visible()
 * @method static Builder|ManualCampaign whereAccountId($value)
 * @method static Builder|ManualCampaign whereBundleId($value)
 * @method static Builder|ManualCampaign whereCreatedAt($value)
 * @method static Builder|ManualCampaign whereCreo($value)
 * @method static Builder|ManualCampaign whereId($value)
 * @method static Builder|ManualCampaign whereName($value)
 * @method static Builder|ManualCampaign whereUpdatedAt($value)
 * @method static Builder|ManualCampaign whereUtmKey($value)
 * @mixin \Eloquent
 */
class ManualCampaign extends Model
{
    use RecordEvents;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_campaigns';

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
     * Map model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving'    => Saving::class,
        'created'   => Created::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bundle()
    {
        return $this->belongsTo(ManualBundle::class, 'bundle_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(ManualAccount::class, 'account_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            ManualAccount::class,
            'account_id',
            'id',
            'account_id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function offer()
    {
        return $this->hasOneThrough(
            Offer::class,
            ManualBundle::class,
            'id',
            'id',
            'bundle_id',
            'offer_id',
        );
    }

    public function scopeVisible(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('manual_campaigns.created_at', '<', '2021-11-05 00:00:00');
        }

        if (! auth()->user()->isAdmin()) {
            $builder->whereHas('account', fn ($query) => $query->visible());
        }

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
            $builder->whereHas('bundle', function (Builder $query) {
                return $query->whereIn('manual_bundles.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
            });
        }

        return $builder;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function insights()
    {
        return $this->hasMany(ManualInsight::class, 'campaign_id');
    }
}
