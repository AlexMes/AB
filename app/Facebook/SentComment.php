<?php

namespace App\Facebook;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Facebook\SentComment
 *
 * @property int $id
 * @property int $profile_id
 * @property string $ad_id
 * @property string $comment_id
 * @property string $message
 * @property string|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Facebook\Ad $ad
 * @property-read \App\Facebook\Profile $profile
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentComment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SentComment extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_sent_comments';

    protected $guarded = [];

    /**
     * Sender facebook profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Ad which received comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
