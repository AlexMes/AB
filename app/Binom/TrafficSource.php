<?php

namespace App\Binom;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Binom\TrafficSource
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $binom_id
 * @property int|null $ts_id
 * @property int|null $traffic_source_id
 * @property-read \App\Binom|null $binom
 * @property-read \App\TrafficSource|null $innerTrafficSource
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereBinomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereTrafficSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereTsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TrafficSource extends Model
{
    protected $table = 'binom_traffic_sources';

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function binom()
    {
        return $this->belongsTo(\App\Binom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function innerTrafficSource()
    {
        return $this->belongsTo(\App\TrafficSource::class, 'traffic_source_id');
    }
}
