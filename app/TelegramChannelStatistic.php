<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TelegramChannelStatistic
 *
 * @property string $date
 * @property int $channel_id
 * @property string $cost
 * @property int $impressions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $id
 * @property-read \App\TelegramChannel $channel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelStatistic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TelegramChannelStatistic extends Model
{
    /**
     * Guarded model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Related telegram channel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(TelegramChannel::class, 'channel_id');
    }
}
