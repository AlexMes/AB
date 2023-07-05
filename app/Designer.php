<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Designer
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Designer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Designer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Designer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Designer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Designer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Designer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Designer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Designer extends Model
{
    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];
}
