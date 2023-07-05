<?php

namespace App\Facebook;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Facebook\Comment
 *
 * @property string $id
 * @property string|null $account_id
 * @property int|null $page_id
 * @property string $published_at
 * @property string $text
 * @property string $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Facebook\Account|null $account
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_comments_log';

    /**
     * Attributes protected from mass-assignment
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Disable incrementing primary key
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Related Facebook ads account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
