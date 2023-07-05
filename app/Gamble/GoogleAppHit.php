<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\GoogleAppHit
 *
 * @property int $id
 * @property int $app_id
 * @property int|null $link_id
 * @property int|null $user_id
 * @property bool $is_passed
 * @property string|null $geo
 * @property string $ip
 * @property string $url
 * @property string|null $utm_source
 * @property string|null $utm_campaign
 * @property string|null $utm_content
 * @property string|null $utm_term
 * @property string|null $utm_medium
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Gamble\GoogleApp $app
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereGeo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereIsPassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUtmContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUtmMedium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUtmSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleAppHit whereUtmTerm($value)
 * @mixin \Eloquent
 */
class GoogleAppHit extends Model
{
    /**
     * Application that instantiated hit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {
        return $this->belongsTo(GoogleApp::class);
    }
}
