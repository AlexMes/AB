<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\Account
 *
 * @property int $id
 * @property string $account_id
 * @property string $name
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Gamble\Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Gamble\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \App\Gamble\User|null $user
 *
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account query()
 * @method static Builder|Account visible()
 * @method static Builder|Account whereAccountId($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @method static Builder|Account whereUserId($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'gamble_accounts';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'gamble_account_gamble_group');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isGambler()) {
            $builder->where('user_id', auth()->id());
        }

        return $builder;
    }
}
