<?php

namespace App\Facebook;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Facebook\ProfilePage
 *
 * @property int $id
 * @property string $name
 * @property string $access_token
 * @property int $profile_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Facebook\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Facebook\Profile $profile
 *
 * @method static Builder|ProfilePage newModelQuery()
 * @method static Builder|ProfilePage newQuery()
 * @method static Builder|ProfilePage query()
 * @method static Builder|ProfilePage visible()
 * @method static Builder|ProfilePage whereAccessToken($value)
 * @method static Builder|ProfilePage whereCreatedAt($value)
 * @method static Builder|ProfilePage whereId($value)
 * @method static Builder|ProfilePage whereName($value)
 * @method static Builder|ProfilePage whereProfileId($value)
 * @method static Builder|ProfilePage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProfilePage extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_profile_pages';

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
     * Related Facebook profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Related Facebook comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'page_id');
    }

    /**
     * Scope profile pages to visible
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->check() && auth()->user()->isVerifier()) {
            $builder->whereHas('profile', fn ($q) => $q->where('user_id', auth()->id()));
        }

        return $builder;
    }
}
