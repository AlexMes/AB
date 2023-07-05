<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\GoogleAppLink
 *
 * @property int $id
 * @property int $app_id
 * @property int $user_id
 * @property bool $enabled
 * @property string|null $geo
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Support\Collection $countries
 * @property-read \App\Gamble\User $user
 *
 * @method static Builder|GoogleAppLink newModelQuery()
 * @method static Builder|GoogleAppLink newQuery()
 * @method static Builder|GoogleAppLink query()
 * @method static Builder|GoogleAppLink whereAppId($value)
 * @method static Builder|GoogleAppLink whereCreatedAt($value)
 * @method static Builder|GoogleAppLink whereEnabled($value)
 * @method static Builder|GoogleAppLink whereGeo($value)
 * @method static Builder|GoogleAppLink whereId($value)
 * @method static Builder|GoogleAppLink whereUpdatedAt($value)
 * @method static Builder|GoogleAppLink whereUrl($value)
 * @method static Builder|GoogleAppLink whereUserId($value)
 * @mixin \Eloquent
 */
class GoogleAppLink extends Model
{
    /**
     * Protect attributes from mass-assignment
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['countries'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Countries for certain link from geo codes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCountriesAttribute()
    {
        $codes = !empty($this->attributes['geo']) ? explode(',', $this->attributes['geo']) : [];

        return Country::all()
            ->filter(fn (Country $country) => in_array($country->code, $codes))
            ->flatten();
    }
}
