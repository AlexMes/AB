<?php

namespace App;

use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualTrafficSource
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 *
 * @method static Builder|ManualTrafficSource allowed()
 * @method static Builder|ManualTrafficSource newModelQuery()
 * @method static Builder|ManualTrafficSource newQuery()
 * @method static Builder|ManualTrafficSource query()
 * @method static Builder|ManualTrafficSource whereCreatedAt($value)
 * @method static Builder|ManualTrafficSource whereId($value)
 * @method static Builder|ManualTrafficSource whereName($value)
 * @method static Builder|ManualTrafficSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualTrafficSource extends Model
{
    use RecordEvents;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table   = 'manual_traffic_sources';

    protected $guarded = ['id'];

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowed(Builder $builder)
    {
        return $builder;
    }
}
