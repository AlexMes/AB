<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Cloak
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cloak whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cloak extends Model
{
    /**
     * @var string
     */
    protected $table = 'cloaks';

    /**
     * @var array
     */
    protected $guarded = [];
}
