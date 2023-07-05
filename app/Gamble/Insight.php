<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\Insight
 *
 * @property int $id
 * @property string $date
 * @property int $account_id
 * @property int $campaign_id
 * @property int $impressions
 * @property int $installs
 * @property string $spend
 * @property int $registrations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $google_app_id
 * @property string $pour_type
 * @property string|null $target
 * @property int $sales
 * @property string $deposit_sum
 * @property int $deposit_cnt
 * @property string|null $optimization_goal
 * @property-read \App\Gamble\Account $account
 * @property-read \App\Gamble\Campaign $campaign
 * @property-read \App\Gamble\GoogleApp|null $googleApp
 *
 * @method static Builder|Insight newModelQuery()
 * @method static Builder|Insight newQuery()
 * @method static Builder|Insight query()
 * @method static Builder|Insight visible()
 * @method static Builder|Insight whereAccountId($value)
 * @method static Builder|Insight whereCampaignId($value)
 * @method static Builder|Insight whereCreatedAt($value)
 * @method static Builder|Insight whereDate($value)
 * @method static Builder|Insight whereDepositCnt($value)
 * @method static Builder|Insight whereDepositSum($value)
 * @method static Builder|Insight whereGoogleAppId($value)
 * @method static Builder|Insight whereId($value)
 * @method static Builder|Insight whereImpressions($value)
 * @method static Builder|Insight whereInstalls($value)
 * @method static Builder|Insight whereOptimizationGoal($value)
 * @method static Builder|Insight wherePourType($value)
 * @method static Builder|Insight whereRegistrations($value)
 * @method static Builder|Insight whereSales($value)
 * @method static Builder|Insight whereSpend($value)
 * @method static Builder|Insight whereTarget($value)
 * @method static Builder|Insight whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Insight extends Model
{
    public const POUR_MANUAL = 'manual';

    public const POUR_AUTO   = 'auto';

    public const POUR_TYPES = [
        self::POUR_MANUAL,
        self::POUR_AUTO,
    ];

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'gamble_insights';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function googleApp()
    {
        return $this->belongsTo(GoogleApp::class);
    }

    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isGambler()) {
            $builder->whereHas('account', fn ($query) => $query->where('user_id', auth()->id()));
        }

        return $builder;
    }
}
