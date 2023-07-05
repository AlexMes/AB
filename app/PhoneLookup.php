<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PhoneLookup
 *
 * @property int $id
 * @property string $phone
 * @property string $country
 * @property string $prefix
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup query()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneLookup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PhoneLookup extends Model
{
    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = ['id'];
}
