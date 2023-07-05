<?php

namespace App;

use App\Events\Orders\Completed;
use App\Events\Orders\Created;
use App\Events\Orders\Updated;
use App\Facebook\Ad;
use App\Traits\AppendAccessAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @property int $id
 * @property int|null $links_count
 * @property int|null $binom_id
 * @property string|null $linkType
 * @property \App\Registrar|null $registrar
 * @property bool $useCloudflare
 * @property bool $useConstructor
 * @property int|null $landing_id
 * @property \Illuminate\Support\Carbon|null $deadline_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $links_done_count
 * @property int|null $sp_id
 * @property int|null $bp_id
 * @property int|null $registrar_id
 * @property int|null $cloak_id
 * @property int|null $hosting_id
 * @property-read \Illuminate\Database\Eloquent\Collection|Ad[] $ads
 * @property-read int|null $ads_count
 * @property-read \App\Page|null $bp
 * @property-read \App\Cloak|null $cloak
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain[] $domains
 * @property-read int|null $domains_count
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 * @property-read \App\Hosting|null $hosting
 * @property-read \App\Domain|null $landing
 * @property-read \App\Page|null $sp
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order overdue()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBinomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCloakId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeadlineAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereHostingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLandingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLinksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLinksDoneCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRegistrar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRegistrarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUseCloudflare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUseConstructor($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use AppendAccessAttributes;

    public const TYPE_DOMAINS = 'domains';

    public const TYPE_SUBDOMAINS = 'subdomains';

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Cast attributes to Carbon instances
     *
     * @var array
     */
    public $dates = [
        'due_date',
        'deadline_at'
    ];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Created::class,
        'updated' => Updated::class,
    ];

    /**
     * Append attributes to model
     *
     * @var array
     */
    protected $appends = [
        'can_update'
    ];

    /**
     * Format date before save
     *
     * @param $value
     */
    public function setDeadlineAtAttribute($value)
    {
        $this->attributes['deadline_at'] = Carbon::createFromFormat('Y-m-d H:i', $value);
    }

    /**
     * Get all order domains
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Update order progress
     *
     * @return \App\Order|bool
     */
    public function updateProgress()
    {
        tap($this)->update([
            'links_done_count' => $this->domains()->ready()->count()
        ]);

        if ($this->isCompleted()) {
            Completed::dispatch($this);
        }

        return $this;
    }

    /**
     * Determine is order completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->links_count === $this->links_done_count;
    }

    /**
     * Determine is order completed
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->isCompleted() ? false : $this->deadline_at->$this->links_done_count;
    }

    /**
     * Get only orders, where deadline is in the past
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeOverdue($builder)
    {
        $builder->where('deadline_at', '<', now()->toDateTimeString());
    }

    /**
     * Safe page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sp()
    {
        return $this->belongsTo(Page::class, 'sp_id');
    }

    /**
     * Black page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bp()
    {
        return $this->belongsTo(Page::class, 'bp_id');
    }

    /**
     * Landing url
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function landing()
    {
        return $this->belongsTo(Domain::class, 'landing_id');
    }

    /**
     * Registrar to use
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registrar()
    {
        return $this->belongsTo(Registrar::class);
    }

    /**
     * Cloak to use
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cloak()
    {
        return $this->belongsTo(Cloak::class);
    }


    /**
     * Hoster to use
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hosting()
    {
        return $this->belongsTo(Hosting::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ads()
    {
        return $this->hasManyThrough(Ad::class, Domain::class);
    }
}
