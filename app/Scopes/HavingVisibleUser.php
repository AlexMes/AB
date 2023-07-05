<?php

namespace App\Scopes;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HavingVisibleUser implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (app()->runningInConsole()
            || auth()->check() && auth()->user()->isAdmin()
            || auth()->check() && auth()->user()->isDeveloper()) {
            return $builder;
        }

        return $builder->joinSub(User::visible()->select(['id','branch_id']), 'visible', $model->getTable().'.user_id', '=', 'visible.id');
    }
}
