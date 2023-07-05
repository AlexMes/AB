<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Issue
 *
 * @property int $id
 * @property string $issuable_id
 * @property string $issuable_type
 * @property string $message
 * @property string|null $fixed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $issuable
 *
 * @method static Builder|Issue closed()
 * @method static Builder|Issue newModelQuery()
 * @method static Builder|Issue newQuery()
 * @method static Builder|Issue pending()
 * @method static Builder|Issue query()
 * @method static Builder|Issue whereCreatedAt($value)
 * @method static Builder|Issue whereFixedAt($value)
 * @method static Builder|Issue whereId($value)
 * @method static Builder|Issue whereIssuableId($value)
 * @method static Builder|Issue whereIssuableType($value)
 * @method static Builder|Issue whereMessage($value)
 * @method static Builder|Issue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Issue extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'issues';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Owning model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function issuable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for open active issues
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    public function scopePending(Builder $query)
    {
        $query->whereNull('fixed_at');
    }

    /**
     * Scope for closed issues
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeClosed(Builder $query)
    {
        $query->whereNotNull('fixed_at');
    }

    /**
     * Mark issue as fixed
     *
     * @return bool
     */
    public function clear()
    {
        return $this->update(['fixed_at' => now()]);
    }
}
