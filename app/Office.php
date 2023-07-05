<?php

namespace App;

use App\CRM\Tenant;
use App\Events\Offices\Created;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Office
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $frx_office_id
 * @property string|null $cpa
 * @property string|null $cpl
 * @property int|null $destination_id
 * @property int|null $frx_tenant_id
 * @property bool $allow_transfer
 * @property bool $distribution_enabled
 * @property string|null $default_start_at
 * @property string|null $default_stop_at
 * @property string $vertical
 * @property bool $is_cp
 * @property bool $disallow_caucasian
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Branch[] $branches
 * @property-read int|null $branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Deposit[] $deposits
 * @property-read int|null $deposits_count
 * @property-read \App\LeadDestination|null $destination
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manager[] $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Branch[] $morningBranches
 * @property-read int|null $morning_branches_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadsOrder[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OfficePayment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Result[] $results
 * @property-read int|null $results_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\StatusConfig[] $statusConfigs
 * @property-read int|null $status_configs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OfficeStatus[] $statuses
 * @property-read int|null $statuses_count
 * @property-read Tenant|null $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Office completedPayments()
 * @method static Builder|Office newModelQuery()
 * @method static Builder|Office newQuery()
 * @method static Builder|Office query()
 * @method static Builder|Office visible()
 * @method static Builder|Office whereAllowTransfer($value)
 * @method static Builder|Office whereCpa($value)
 * @method static Builder|Office whereCpl($value)
 * @method static Builder|Office whereCreatedAt($value)
 * @method static Builder|Office whereDefaultStartAt($value)
 * @method static Builder|Office whereDefaultStopAt($value)
 * @method static Builder|Office whereDestinationId($value)
 * @method static Builder|Office whereDisallowCaucasian($value)
 * @method static Builder|Office whereDistributionEnabled($value)
 * @method static Builder|Office whereFrxOfficeId($value)
 * @method static Builder|Office whereFrxTenantId($value)
 * @method static Builder|Office whereId($value)
 * @method static Builder|Office whereIsCp($value)
 * @method static Builder|Office whereName($value)
 * @method static Builder|Office whereUpdatedAt($value)
 * @method static Builder|Office whereVertical($value)
 * @mixin \Eloquent
 */
class Office extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'offices';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'heads' => 'json',
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Created::class
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Related office deposits
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Orders for leads splitter
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(LeadsOrder::class);
    }

    /**
     * Users related to office
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Managers assigned to office
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    /**
     * Limit offices to visible
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth('api')->check() && auth()->user()->isAdmin()) {
            return $builder;
        }

        if ((auth('api')->check() || auth('web')->check()) && auth()->user()->hasRole([User::HEAD,User::SUPPORT,User::SUBSUPPORT,User::TEAMLEAD])) {
            return $builder->whereIn('offices.id', auth()->user()->branch->offices()->pluck('offices.id'));
        }

        if (auth('web')->check()) {
            return $builder;
        }

        if (auth('crm')->id() === 3761) {
            return $builder->whereIn('offices.id', [8,20,25,83,108,118]);
        }

        if (!auth('crm')->user()->hasTenant()) {
            return $builder->whereId(auth('crm')->user()->office_id);
        }

        // Allow users with root access view all offices from related tenant
        if (auth('crm')->user()->frx_role == 'root') {
            return $builder->where('frx_tenant_id', auth('crm')->user()->frx_tenant_id);
        }

        return $builder->where('id', auth('crm')->user()->office_id);
    }

    /**
     * Scopes offices which have payments and it's completed.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeCompletedPayments(Builder $builder)
    {
        return $builder->whereHas('payments')
            ->whereDoesntHave('payments', fn ($query) => $query->whereColumn('paid', '>', 'assigned'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destination()
    {
        return $this->belongsTo(LeadDestination::class, 'destination_id');
    }

    /**
     * TCRM-FRX setup instance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'frx_tenant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses()
    {
        return $this->hasMany(OfficeStatus::class, 'office_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusConfigs()
    {
        return $this->hasMany(StatusConfig::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(OfficePayment::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_offices', 'office_id');
    }

    public function morningBranches()
    {
        return $this->belongsToMany(Branch::class, 'morning_branch_office', 'office_id')
            ->withPivot(['time', 'duration']);
    }
}
