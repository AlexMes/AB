<?php

namespace App;

use App\Http\Controllers\TrafficSources\Domains;
use Illuminate\Database\Eloquent\Model;

/**
 * App\TrafficSource
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain[] $domains
 * @property-read int|null $domains_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrafficSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TrafficSource extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table   = 'traffic_sources';

    protected $guarded = ['id'];

    /**
     * Assigned domains
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}
