<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisibleToUser implements Scope
{
    /**
     * Apply visibility limitations
     * to models.
     *
     * @param Builder $builder
     * @param Model   $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder, Model $model)
    {
//        if (auth()->check() && in_array(auth()->id(), [37,7])) {
//            return $builder;
//        }
//        if (auth()->check() && optional(auth()->user())->isBuyer()) {
//            return $builder->where(fn ($query) => $query->where('user_id', auth()->id())->orWhereNull('user_id'));
//        }

        return $builder;
    }
}
