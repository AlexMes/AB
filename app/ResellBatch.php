<?php

namespace App;

use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\ResellBatch
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $registered_at
 * @property array $filters
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $assign_until
 * @property bool $create_offer
 * @property bool $simulate_autologin
 * @property int|null $branch_id
 * @property int|null $substitute_offer
 * @property bool $ignore_paused_routes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Office[] $offices
 * @property-read int|null $offices_count
 * @property-read \App\Offer|null $substituteOffer
 *
 * @method static Builder|ResellBatch autoCreated()
 * @method static Builder|ResellBatch inProcess()
 * @method static Builder|ResellBatch incomplete()
 * @method static Builder|ResellBatch newModelQuery()
 * @method static Builder|ResellBatch newQuery()
 * @method static Builder|ResellBatch query()
 * @method static Builder|ResellBatch visible()
 * @method static Builder|ResellBatch whereAssignUntil($value)
 * @method static Builder|ResellBatch whereBranchId($value)
 * @method static Builder|ResellBatch whereCreateOffer($value)
 * @method static Builder|ResellBatch whereCreatedAt($value)
 * @method static Builder|ResellBatch whereFilters($value)
 * @method static Builder|ResellBatch whereId($value)
 * @method static Builder|ResellBatch whereIgnorePausedRoutes($value)
 * @method static Builder|ResellBatch whereName($value)
 * @method static Builder|ResellBatch whereRegisteredAt($value)
 * @method static Builder|ResellBatch whereSimulateAutologin($value)
 * @method static Builder|ResellBatch whereStatus($value)
 * @method static Builder|ResellBatch whereSubstituteOffer($value)
 * @method static Builder|ResellBatch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ResellBatch extends Model
{
    use RecordEvents;

    public const PENDING    = 'pending';
    public const IN_PROCESS = 'in_process';
    public const PAUSED     = 'paused';
    public const CANCELED   = 'canceled';
    public const FINISHED   = 'finished';

    /**
     * Cast model attributes to native type
     *
     * @var array
     */
    protected $casts = [
        'registered_at' => 'datetime',
        'assign_until'  => 'datetime',
        'filters'       => 'array',
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'resell_batches';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function leads()
    {
        return $this->belongsToMany(Lead::class, null, 'batch_id')
            ->withPivot(['assigned_at', 'assignment_id']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function offices()
    {
        return $this->belongsToMany(Office::class, null, 'batch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function substituteOffer()
    {
        return $this->belongsTo(Offer::class, 'substitute_offer');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeInProcess(Builder $builder)
    {
        return $builder->where('status', static::IN_PROCESS);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeIncomplete(Builder $builder)
    {
        return $builder->whereHas('leads', fn ($query) => $query->whereNull('assigned_at'));
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isAdmin()) {
            return $builder;
        }

        return $builder->where('resell_batches.branch_id', auth()->user()->branch_id);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAutoCreated(Builder $builder)
    {
        return $builder->where('resell_batches.name', 'similar to', 'Branch19 [0-9]{4}-[0-9]{2}-[0-9]{2}');
    }

    public function getRegisteredAtAttribute($value)
    {
        if ($value !== null) {
            return Carbon::parse($value) ;
        }

        return now();
    }
}
