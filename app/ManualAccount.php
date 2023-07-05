<?php

namespace App;

use App\Deluge\Events\Accounts\Saved;
use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\ManualAccount
 *
 * @property int $id
 * @property string $account_id
 * @property string $name
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $status
 * @property string $moderation_status
 * @property string|null $creo
 * @property int|null $supplier_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualCampaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read mixed $spend
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualInsight[] $insights
 * @property-read int|null $insights_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualAccountModerationLog[] $moderationLogs
 * @property-read int|null $moderation_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualPour[] $pours
 * @property-read int|null $pours_count
 * @property-read \App\ManualSupplier|null $supplier
 * @property-read \App\User|null $user
 *
 * @method static Builder|ManualAccount newModelQuery()
 * @method static Builder|ManualAccount newQuery()
 * @method static Builder|ManualAccount query()
 * @method static Builder|ManualAccount visible()
 * @method static Builder|ManualAccount whereAccountId($value)
 * @method static Builder|ManualAccount whereCreatedAt($value)
 * @method static Builder|ManualAccount whereCreo($value)
 * @method static Builder|ManualAccount whereId($value)
 * @method static Builder|ManualAccount whereModerationStatus($value)
 * @method static Builder|ManualAccount whereName($value)
 * @method static Builder|ManualAccount whereStatus($value)
 * @method static Builder|ManualAccount whereSupplierId($value)
 * @method static Builder|ManualAccount whereUpdatedAt($value)
 * @method static Builder|ManualAccount whereUserId($value)
 * @method static Builder|ManualAccount withLifetime(?\Illuminate\Support\Carbon $since = null, ?\Illuminate\Support\Carbon $until = null)
 * @method static Builder|ManualAccount withSpend(?\Illuminate\Support\Carbon $since = null, ?\Illuminate\Support\Carbon $until = null)
 * @mixin \Eloquent
 */
class ManualAccount extends Model
{
    use RecordEvents;

    public const ACTIVE = 'active';

    public const BLOCKED = 'blocked';

    public const STATUSES = [
        self::ACTIVE,
        self::BLOCKED,
    ];

    public const MODERATION_REVIEW      = 'review';
    public const MODERATION_APPROVED    = 'approved';
    public const MODERATION_DISAPPROVED = 'disapproved';

    public const MODERATION_STATUSES = [
        self::MODERATION_REVIEW,
        self::MODERATION_APPROVED,
        self::MODERATION_DISAPPROVED,
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_accounts';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Map model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => Saved::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(
            ManualGroup::class,
            null,
            'account_id',
            'group_id',
            'account_id',
            'id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pours()
    {
        return $this->belongsToMany(
            ManualPour::class,
            null,
            'account_id',
            'pour_id',
            'account_id',
            'id',
        )->withTimestamps()
            ->withPivot(['id', 'status', 'moderation_status']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moderationLogs()
    {
        return $this->hasMany(ManualAccountModerationLog::class, 'account_id', 'account_id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->id() === 230) {
            return $builder->where('manual_accounts.created_at', '<', '2021-11-05 00:00:00');
        }

        if (! auth()->user()->isAdmin()) {
            $builder->where('manual_accounts.created_at', '>', now()->subYear()->toDateTimeString())->whereIn('manual_accounts.user_id', User::visible()->pluck('id'));
        }

        return $builder->where('manual_accounts.created_at', '>', now()->subYear()->toDateTimeString());
    }

    /**
     * Account campaigns
     *
     * @return void
     */
    public function campaigns()
    {
        return $this->hasMany(ManualCampaign::class, 'account_id', 'account_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param Carbon|null                           $since
     * @param Carbon|null                           $until
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSpend(Builder $builder, ?Carbon $since = null, ?Carbon $until = null)
    {
        return $builder->addSelect([
            'spend' => ManualInsight::allowedOffers()
                ->selectRaw('sum(spend)')
                ->when($since, fn ($query) => $query->whereDate('date', '>=', $since))
                ->when($until, fn ($query) => $query->whereDate('date', '<=', $until))
                ->whereColumn('manual_insights.account_id', '=', 'manual_accounts.account_id'),
        ]);
    }

    public function getSpendAttribute()
    {
        return array_key_exists('spend', $this->attributes)
            ? $this->attributes['spend']
            : ManualInsight::allowedOffers()->where('account_id', $this->account_id)->sum('spend');
    }

    /**
     * @param Builder     $builder
     * @param Carbon|null $since
     * @param Carbon|null $until
     *
     * @return Builder
     */
    public function scopeWithLifetime(Builder $builder, ?Carbon $since = null, ?Carbon $until = null)
    {
        return $builder->addSelect([
            'lifetime' => ManualAccountManualPour::query()
                ->select([
                    DB::raw('EXTRACT(EPOCH FROM age(manual_pours.date, manual_accounts.created_at::date))::int / 60')
                ])
                ->when($since, function (Builder $query) use ($since) {
                    return $query->whereDate('manual_pours.date', '>=', $since->toDateString())
                        ->select([
                            DB::raw('EXTRACT(EPOCH FROM age(
                                    manual_pours.date,
                                    CASE WHEN \'' . $since->toDateString() . '\' > manual_accounts.created_at::date
                                        THEN \'' . $since->toDateString() . '\'
                                        ELSE manual_accounts.created_at::date
                                    END
                                ))::int / 60')
                        ]);
                })
                ->when($until, function (Builder $query) use ($until) {
                    return $query->whereDate('manual_pours.date', '<=', $until->toDateString());
                })
                ->join('manual_pours', 'manual_account_manual_pour.pour_id', 'manual_pours.id')
                ->whereColumn('manual_account_manual_pour.account_id', '=', 'manual_accounts.account_id')
                ->where('manual_account_manual_pour.status', ManualAccount::ACTIVE)
                ->orderByDesc('manual_pours.date')
                ->limit(1)
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(ManualSupplier::class, 'supplier_id');
    }

    /**
     * Account insights
     *
     * @return void
     */
    public function insights()
    {
        return $this->hasMany(ManualInsight::class, 'account_id', 'account_id');
    }
}
