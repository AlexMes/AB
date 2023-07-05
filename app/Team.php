<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Team
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $branch_id
 * @property-read \App\Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Team newModelQuery()
 * @method static Builder|Team newQuery()
 * @method static Builder|Team query()
 * @method static Builder|Team visible()
 * @method static Builder|Team whereBranchId($value)
 * @method static Builder|Team whereCreatedAt($value)
 * @method static Builder|Team whereId($value)
 * @method static Builder|Team whereName($value)
 * @method static Builder|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Team extends Model
{
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, TeamUser::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (app()->runningInConsole() || auth()->user()->isAdmin()) {
            return $builder;
        }

        if (auth()->check() && auth()->user()->isBranchHead()) {
            return $builder->where('branch_id', auth()->user()->branch_id);
        }

        if (auth()->check() && auth()->user()->isSupport()) {
            return $builder->where('branch_id', auth()->user()->branch_id)
                ->orWhereHas('users', fn ($query) => $query->where('user_id', auth()->id()));
        }

        return $builder->whereHas('users', fn ($query) => $query->where('user_id', auth()->id()));
    }
}
