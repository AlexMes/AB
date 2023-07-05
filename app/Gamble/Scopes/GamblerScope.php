<?php


namespace App\Gamble\Scopes;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GamblerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where(function (Builder $query) {
            $query->whereIn('role', [User::GAMBLE_ADMIN, User::GAMBLER])
                ->orWhereIn('id', [1,3]);
        });
    }
}
