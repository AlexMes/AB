<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualPour
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualAccount[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \App\User $user
 *
 * @method static Builder|ManualPour newModelQuery()
 * @method static Builder|ManualPour newQuery()
 * @method static Builder|ManualPour query()
 * @method static Builder|ManualPour visible()
 * @method static Builder|ManualPour whereCreatedAt($value)
 * @method static Builder|ManualPour whereDate($value)
 * @method static Builder|ManualPour whereId($value)
 * @method static Builder|ManualPour whereUpdatedAt($value)
 * @method static Builder|ManualPour whereUserId($value)
 * @mixin \Eloquent
 */
class ManualPour extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_pours';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast attributes to Carbon instances
     *
     * @var array
     */
    protected $dates = ['date'];

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
    public function accounts()
    {
        return $this->belongsToMany(
            ManualAccount::class,
            null,
            'pour_id',
            'account_id',
            'id',
            'account_id',
        )->withTimestamps()
            ->withPivot(['id', 'status', 'moderation_status']);
    }

    /**
     * Scopes pours for the auth user
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (! auth()->user()->isAdmin()) {
            $builder->whereIn('user_id', User::visible()->pluck('id'));
        }

        return $builder;
    }
}
