<?php

namespace App;

use App\Services\SmsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Branch
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $stats_access
 * @property string|null $telegram_id
 * @property string|null $sms_service
 * @property array|null $sms_config
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $allowedUsers
 * @property-read int|null $allowed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Office[] $offices
 * @property-read int|null $offices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Branch allowed()
 * @method static Builder|Branch newModelQuery()
 * @method static Builder|Branch newQuery()
 * @method static Builder|Branch query()
 * @method static Builder|Branch validSmsService()
 * @method static Builder|Branch whereCreatedAt($value)
 * @method static Builder|Branch whereId($value)
 * @method static Builder|Branch whereName($value)
 * @method static Builder|Branch whereSmsConfig($value)
 * @method static Builder|Branch whereSmsService($value)
 * @method static Builder|Branch whereStatsAccess($value)
 * @method static Builder|Branch whereTelegramId($value)
 * @method static Builder|Branch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Branch extends Model
{
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'sms_config' => 'json',
    ];

    /**
     * Users of the branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'allowed_branches');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowed(Builder $builder)
    {
        if (auth('crm')->check()) {
            if (in_array(auth('crm')->user()->frx_role, ['admin','root'])) {
                return $builder;
            }
            if (auth('crm')->user()->hasElevatedPrivileges()) {
                return $builder->whereHas('users.leads.assignments.route.order', function ($query) {
                    return $query->where('leads_orders.office_id', auth('crm')->user()->office_id);
                });
            }

            return $builder->whereHas('users.leads.assignments.route', function ($query) {
                return $query->where('lead_order_routes.manager_id', auth('crm')->id());
            });
        }

        if (in_array(auth()->id(), [37,130,132])) {
            return $builder;
        }

        if (auth()->user()->hasRole(['head','teamlead','buyer','support','designer'])) {
            return $builder->where('branches.id', auth()->user()->branch_id);
        }

        if (auth()->user()->isDesigner()) {
            $builder->whereIn('branches.id', auth()->user()->branch_id);
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeValidSmsService(Builder $builder)
    {
        return $builder->whereIn('sms_service', \App\Enums\SmsService::LIST)
            ->whereNotNull('sms_config');
    }

    /**
     * Offices opened for the branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function offices()
    {
        return $this->belongsToMany(Office::class, 'branch_offices', 'branch_id');
    }

    /**
     * @return SmsService|null
     */
    public function initializeSmsService()
    {
        if ($this->sms_service === null || $this->sms_config === null) {
            return null;
        }

        return app($this->sms_service, ['config' => $this->sms_config]);
    }

    /**
     * @return bool
     */
    public function isSmsServiceValid()
    {
        return in_array($this->sms_service, \App\Enums\SmsService::LIST) && $this->sms_config !== null;
    }
}
