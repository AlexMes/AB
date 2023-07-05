<?php

namespace App;

use App\Events\LeadDestinationUpdated;
use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LeadDestination
 *
 * @property int $id
 * @property string $name
 * @property string|null $driver
 * @property array|null $configuration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $autologin
 * @property int|null $office_id
 * @property int|null $branch_id
 * @property array|null $test_payload
 * @property array|null $status_map
 * @property bool $is_active
 * @property string|null $collect_results_error
 * @property string|null $collect_statuses_error
 * @property bool $land_autologin
 * @property bool $deposit_notification
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderAssignment[] $assignments
 * @property-read int|null $assignments_count
 * @property-read \App\Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read array|null $configuration_keys
 * @property-read \App\LeadDestinationDriver|\App\LeadDestinationDriver[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null $service
 * @property-read \App\Office|null $office
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Office[] $offices
 * @property-read int|null $offices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadsOrder[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LeadOrderRoute[] $routes
 * @property-read int|null $routes_count
 *
 * @method static Builder|LeadDestination active()
 * @method static Builder|LeadDestination newModelQuery()
 * @method static Builder|LeadDestination newQuery()
 * @method static Builder|LeadDestination query()
 * @method static Builder|LeadDestination visible()
 * @method static Builder|LeadDestination whereAutologin($value)
 * @method static Builder|LeadDestination whereBranchId($value)
 * @method static Builder|LeadDestination whereCollectResultsError($value)
 * @method static Builder|LeadDestination whereCollectStatusesError($value)
 * @method static Builder|LeadDestination whereConfiguration($value)
 * @method static Builder|LeadDestination whereCreatedAt($value)
 * @method static Builder|LeadDestination whereDepositNotification($value)
 * @method static Builder|LeadDestination whereDriver($value)
 * @method static Builder|LeadDestination whereId($value)
 * @method static Builder|LeadDestination whereIsActive($value)
 * @method static Builder|LeadDestination whereLandAutologin($value)
 * @method static Builder|LeadDestination whereName($value)
 * @method static Builder|LeadDestination whereOfficeId($value)
 * @method static Builder|LeadDestination whereStatusMap($value)
 * @method static Builder|LeadDestination whereTestPayload($value)
 * @method static Builder|LeadDestination whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadDestination extends Model
{
    use RecordEvents;

    public const AUTOLOGIN_ON       = 'on';
    public const AUTOLOGIN_OFF      = 'off';
    public const AUTOLOGIN_OPTIONAL = 'optional';

    public const AUTOLOGIN_OPTIONS = [
        self::AUTOLOGIN_ON,
        self::AUTOLOGIN_OFF,
        self::AUTOLOGIN_OPTIONAL,
    ];

    /**
     * Table name in the database
     *
     * @var string
     */
    protected $table = 'lead_destinations';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'configuration' => 'json',
        'test_payload'  => 'json',
        'status_map'    => 'json',
    ];

    /**
     * Hidden model attributes
     *
     * @var string[]
     */
    protected $hidden = [
        'configuration',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'updated' => LeadDestinationUpdated::class,
    ];

    /**
     * Routes configured with destination
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        return $this->hasMany(LeadOrderRoute::class, 'destination_id');
    }

    /**
     * Offices configured with destination
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offices()
    {
        return $this->hasMany(Office::class, 'destination_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Orders configured with destination
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(LeadsOrder::class, 'destination_id');
    }

    /**
     * System-wide default destination driver
     *
     * @return static
     */
    public static function default()
    {
        return new static([
            'id'     => null,
            'name'   => 'Default',
            'driver' => LeadDestinationDriver::DEFAULT,
        ]);
    }

    /**
     * Get service class for destination
     *
     * @return \App\LeadDestinationDriver|\App\LeadDestinationDriver[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getServiceAttribute()
    {
        return LeadDestinationDriver::find($this->driver);
    }

    /**
     * Initialize driver with configuration
     *
     * @return \App\DestinationDrivers\Contracts\DeliversLeadToDestination|\App\DestinationDrivers\Contracts\GetsInfoFromDestination
     */
    public function initialize()
    {
        return app($this->service->implementation, ['configuration' => $this->configuration]);
    }

    /**
     * Leads assignend through this destination
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignments()
    {
        return $this->hasMany(LeadOrderAssignment::class, 'destination_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope destinations to visible only
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function scopeVisible(Builder $builder)
    {
        if (app()->runningInConsole() || auth()->user()->isAdmin()) {
            return $builder;
        }

        return $builder->where(fn ($query) => $query->where('lead_destinations.branch_id', auth()->user()->branch_id)->orWhereNull('branch_id'));
    }

    /**
     * @return array|null
     */
    public function getConfigurationKeysAttribute()
    {
        if ($this->configuration === null) {
            return null;
        }

        return array_fill_keys(array_keys($this->configuration), null);
    }

    /**
     * Gets internal mapped status if exists and not empty
     * Returns $externalStatus otherwise
     *
     * @param string $externalStatus
     *
     * @return string
     */
    public function getInternalStatus(?string $externalStatus)
    {
        if ($this->status_map === null || $externalStatus === null) {
            return $externalStatus;
        }

        $filtered = array_filter($this->status_map, fn ($item) => $item['external'] === $externalStatus);

        $internalStatus = !empty($filtered)
            ? reset($filtered)['internal']
            : null;

        return $internalStatus ?: $externalStatus;
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', true);
    }
}
