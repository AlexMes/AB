<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TelegramChannelSubject
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TelegramChannel[] $channels
 * @property-read int|null $channels_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramChannelSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TelegramChannelSubject extends Model
{
    protected $guarded = [];

    public function channels()
    {
        return $this->hasMany(TelegramChannel::class, 'subject_id');
    }
}
