<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Hosting
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hosting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Hosting extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'hostings';

    /**
     * @var array
     */
    protected $guarded = [];
}
