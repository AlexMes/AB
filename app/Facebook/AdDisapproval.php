<?php

namespace App\Facebook;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Facebook\AdDisapproval
 *
 * @property int $id
 * @property string $ad_id
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Facebook\Ad $ad
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdDisapproval whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdDisapproval extends Model
{
    /**
     * Guard model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'logs_facebook_ads_disapprovals';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
