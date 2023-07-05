<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CRM\Callback
 *
 * @property int $id
 * @property int $assignment_id
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $call_at
 * @property \Illuminate\Support\Carbon|null $called_at
 * @property string|null $frx_call_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\CRM\LeadOrderAssignment $assignment
 *
 * @method static Builder|Callback incomplete()
 * @method static Builder|Callback newModelQuery()
 * @method static Builder|Callback newQuery()
 * @method static Builder|Callback query()
 * @method static Builder|Callback scheduled()
 * @method static Builder|Callback whereAssignmentId($value)
 * @method static Builder|Callback whereCallAt($value)
 * @method static Builder|Callback whereCalledAt($value)
 * @method static Builder|Callback whereCreatedAt($value)
 * @method static Builder|Callback whereFrxCallId($value)
 * @method static Builder|Callback whereId($value)
 * @method static Builder|Callback wherePhone($value)
 * @method static Builder|Callback whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Callback extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'callbacks';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast model attributes to native type
     *
     * @var array
     */
    protected $casts = [
        'call_at'   => 'datetime',
        'called_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignment()
    {
        return $this->belongsTo(LeadOrderAssignment::class, 'assignment_id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeIncomplete(Builder $builder)
    {
        return $builder->whereNull('called_at');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeScheduled(Builder $builder)
    {
        return $builder->whereNotNull('call_at');
    }
}
