<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Placement
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Placement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Placement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Placement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Placement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Placement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Placement extends Model
{
    protected $guarded = ['id'];
}
