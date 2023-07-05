<?php

namespace App;

use App\Facebook\Profile;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ProfileLog
 *
 * @property int $id
 * @property int $profile_id
 * @property int $duration
 * @property string|null $miniature
 * @property string|null $creative
 * @property string|null $text
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Profile $profile
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereCreative($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereMiniature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProfileLog extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @var string
     */
    protected $table = 'facebook_profile_logs';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
