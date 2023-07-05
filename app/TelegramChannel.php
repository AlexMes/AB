<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\TelegramChannel
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $subject_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TelegramChannelStatistic[] $statistics
 * @property-read int|null $statistics_count
 * @property-read \App\TelegramChannelSubject|null $subject
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel newQuery()
 * @method static \Illuminate\Database\Query\Builder|TelegramChannel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TelegramChannel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TelegramChannel withoutTrashed()
 * @mixin \Eloquent
 */
class TelegramChannel extends Model
{
    use SoftDeletes;

    /**
     * Guarded model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Channel statistics
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statistics()
    {
        return $this->hasMany(TelegramChannelStatistic::class);
    }

    /**
     * Channel subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo(TelegramChannelSubject::class, 'subject_id');
    }
}
