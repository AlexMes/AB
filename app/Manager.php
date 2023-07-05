<?php

namespace App;

use App\CRM\Tenant;
use App\Events\ManagerCreated;
use App\Google\SpreadSheet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * App\Manager
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $spreadsheet_id
 * @property int|null $office_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $frx_user_id
 * @property string|null $frx_role
 * @property string|null $frx_access_token
 * @property int|null $frx_tenant_id
 * @property string|null $password
 * @property string $locale
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderAssignment[] $assignments
 * @property-read int|null $assignments_count
 * @property-read \App\Office|null $office
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderRoute[] $routes
 * @property-read int|null $routes_count
 * @property-write mixed $spread_sheet_id
 * @property-read Tenant|null $tenant
 *
 * @method static Builder|Manager newModelQuery()
 * @method static Builder|Manager newQuery()
 * @method static \Illuminate\Database\Query\Builder|Manager onlyTrashed()
 * @method static Builder|Manager query()
 * @method static Builder|Manager visible()
 * @method static Builder|Manager whereCreatedAt($value)
 * @method static Builder|Manager whereDeletedAt($value)
 * @method static Builder|Manager whereEmail($value)
 * @method static Builder|Manager whereFrxAccessToken($value)
 * @method static Builder|Manager whereFrxRole($value)
 * @method static Builder|Manager whereFrxTenantId($value)
 * @method static Builder|Manager whereFrxUserId($value)
 * @method static Builder|Manager whereId($value)
 * @method static Builder|Manager whereLocale($value)
 * @method static Builder|Manager whereName($value)
 * @method static Builder|Manager whereOfficeId($value)
 * @method static Builder|Manager wherePassword($value)
 * @method static Builder|Manager whereSpreadsheetId($value)
 * @method static Builder|Manager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Manager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Manager withoutTrashed()
 * @mixin \Eloquent
 */
class Manager extends Authenticatable
{
    use SoftDeletes;

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Hidden model attributes
     *
     * @var string[]
     */
    protected $hidden = [
        'access_token', 'password'
    ];

    /**
     * Model events binding
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ManagerCreated::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get manager spreadsheet
     *
     * @return \App\Google\SpreadSheet
     */
    public function spreadsheet()
    {
        return new SpreadSheet($this->spreadsheet_id);
    }

    /**
     * @return bool
     */
    public function hasSpreadSheet()
    {
        return $this->spreadsheet_id !== null;
    }

    /**
     * Get default string for spreadsheet.
     *
     * @return string
     */
    public function getDefaultSpreadSheetName(): string
    {
        return sprintf('[%s] %s', $this->office->name, $this->name);
    }

    /**
     * Format and set spreadsheet id
     *
     * @param $value
     *
     * @return void
     */
    public function setSpreadSheetIdAttribute($value)
    {
        if ($value !== null) {
            $matches = [];
            if (preg_match('/(.*?)(\\/|$)/', $value, $matches)) {
                $this->attributes['spreadsheet_id'] = $matches[1];
            }

            if (preg_match('/\\/d\\/(.*?)(\\/|$)/', $value, $matches)) {
                $this->attributes['spreadsheet_id'] = $matches[1];
            }
        } else {
            $this->attributes['spreadsheet_id'] = null;
        }
    }

    /**
     * Routes for particular manager
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        return $this->hasMany(LeadOrderRoute::class);
    }

    /**
     * Manager assignments
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function assignments()
    {
        return $this->hasManyThrough(LeadOrderAssignment::class, LeadOrderRoute::class, 'manager_id', 'route_id');
    }

    /**
     * All offers, that visible for manager
     *
     *
     * @return mixed
     */
    public function offers()
    {
        return Cache::remember(sprintf('manager-offers-%s', $this->id), now()->addHours(5), function () {
            if (in_array($this->frx_role, ['root','manager'])) {
                return Offer::all();
            }

            return Offer::whereIn('id', $this->assignments()->distinct()->pluck('offer_id'))->get();
        });
    }

    /**
     * Scope managers to visible only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth('web')->check()) {
            return $builder;
        }

        if (optional(auth('crm')->user()->office)->allow_transfer) {
            if (auth('crm')->user()->isAgent()) {
                return $builder->where('office_id', auth('crm')->user()->office_id);
            }
        }

        if (auth('crm')->id() === 3761) {
            return $builder->whereIn('managers.office_id', [8,20,25,83,108,118]);
        }

        if (!auth('crm')->user()->hasTenant()) {
            if (auth('crm')->user()->isAdmin()) {
                return $builder->where('office_id', auth('crm')->user()->office_id);
            }

            return $builder->whereId(auth('crm')->id());
        }

        if (optional(auth('crm')->user())->isOfficeHead()) {
            return $builder->where('office_id', auth('crm')->user()->office_id);
        }

        if (optional(auth('crm')->user())->isRoot()) {
            return $builder->where('frx_tenant_id', auth('crm')->user()->frx_tenant_id);
        }


        return $builder->where('id', auth('crm')->id());
    }

    /**
     * Determine is manager has extended abilities
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->frx_role === 'root' || $this->frx_role === 'admin';
    }

    /**
     * Determines is manager can see other managers in offices.
     *
     * @return bool
     */
    public function isOfficeHead()
    {
        return $this->frx_role === 'manager';
    }

    /**
     * Determines is manager can see other managers in offices.
     *
     * @return bool
     */
    public function isCloser()
    {
        return $this->frx_role === 'closer';
    }

    /**
     * @return bool
     */
    public function isAgent()
    {
        return $this->frx_role === 'agent';
    }

    /**
     * Determine is authenticated user is admin or office manager
     *
     * @return bool|void
     */
    public function hasElevatedPrivileges()
    {
        return in_array($this->frx_role, ['root','manager','admin']);
    }

    /**
     * Aint stupid if it work
     */
    public function isBuyer()
    {
        return false;
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
     * Determine if the manager has a tenant
     *
     * @return bool
     */
    public function hasTenant()
    {
        return $this->frx_tenant_id !== null;
    }

    /**
     * Transfers manager's leads and deletes the manager
     *
     * @param Manager[]|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $assignManagers
     *
     * @throws \Throwable
     */
    public function deleteAndTransferLeads($assignManagers)
    {
        $i = 0;
        foreach ($this->assignments as $assignment) {
            $assignment->transfer($assignManagers->get($i++));

            if ($i >= $assignManagers->count()) {
                $i = 0;
            }
        }

        $this->delete();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(fn (Manager $manager) => $manager->routes()->delete());
    }

    public function isRoot()
    {
        return $this->frx_role === 'root';
    }

    /**
     * Hash passwords by default
     *
     * @param string $password
     *
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * @param Office $office
     *
     * @return bool
     */
    public function changeOffice(Office $office): bool
    {
        return $this->update(['office_id' => $office->id]);
    }
}
