<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\Offer
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Offer extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'gamble_offers';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
