<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\ManualGroup
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $branch_id
 * @property string|null $google
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualAccount[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \App\Branch|null $branch
 * @property-read mixed $current_month_approved_percent
 * @property-read mixed $current_month_blocked_percent
 * @property-read mixed $current_month_lifetime
 * @property-read mixed $current_month_spend
 *
 * @method static Builder|ManualGroup newModelQuery()
 * @method static Builder|ManualGroup newQuery()
 * @method static Builder|ManualGroup query()
 * @method static Builder|ManualGroup visible()
 * @method static Builder|ManualGroup whereBranchId($value)
 * @method static Builder|ManualGroup whereCreatedAt($value)
 * @method static Builder|ManualGroup whereGoogle($value)
 * @method static Builder|ManualGroup whereId($value)
 * @method static Builder|ManualGroup whereName($value)
 * @method static Builder|ManualGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualGroup extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_groups';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accounts()
    {
        return $this->belongsToMany(
            ManualAccount::class,
            null,
            'group_id',
            'account_id',
            'id',
            'account_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getCurrentMonthSpendAttribute()
    {
        return array_key_exists('current_month_spend', $this->attributes)
            ? $this->attributes['current_month_spend']
            : $this->accounts()
                ->withSpend(now()->startOfMonth(), now())
                ->get()
                ->sum('spend');
    }

    public function getCurrentMonthApprovedPercentAttribute()
    {
        return ManualAccountManualPour::query()
            ->select([
                DB::raw('CASE WHEN count(*)=0
                        THEN 0
                        ELSE round(count(CASE WHEN moderation_status=\'approved\' THEN 1 END)::decimal / count(*) * 100, 2)
                    END as cnt')
            ])
            ->whereIn('account_id', $this->accounts->pluck('account_id'))
            ->whereHas('pour', function (Builder $builder) {
                return $builder->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->toDateString()]);
            })
            ->first()->cnt;
    }

    public function getCurrentMonthBlockedPercentAttribute()
    {
        return ManualAccountManualPour::query()
            ->select([
                DB::raw('CASE WHEN count(*)=0
                        THEN 0
                        ELSE round(count(CASE WHEN status=\'blocked\' THEN 1 END)::decimal / count(*) * 100, 2)
                    END as cnt')
            ])
            ->whereIn('account_id', $this->accounts->pluck('account_id'))
            ->whereHas('pour', function (Builder $builder) {
                return $builder->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->toDateString()]);
            })
            ->first()->cnt;
    }

    public function getCurrentMonthLifetimeAttribute()
    {
        return array_key_exists('current_month_lifetime', $this->attributes)
            ? $this->attributes['current_month_lifetime']
            : $this->accounts()
                ->withLifetime(now()->startOfMonth(), now())
                ->get()
                ->sum('lifetime');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->hasRole([User::ADMIN, User::DEVELOPER])) {
            return $builder;
        }

        return $builder->where('manual_groups.branch_id', auth()->user()->branch_id)
            ->orWhereNull('manual_groups.branch_id');
    }
}
