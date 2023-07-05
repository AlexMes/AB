<?php

namespace App\Traits;

/**
 * Trait AppendAccessAttributes
 * Automatically append access attributes to JSON
 *
 * @package App\Traits
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait AppendAccessAttributes
{
    /**
     * Determine us current user can create model
     *
     * @return bool
     */
    public function getCanCreateAttribute()
    {
        return optional(auth()->user())->can('store', get_class($this)) ?? false;
    }

    /**
     * Determine us current user can update model
     *
     * @return bool
     */
    public function getCanUpdateAttribute()
    {
        return optional(auth()->user())->can('update', $this) ?? false;
    }

    /**
     * Determine us current user can delete model
     *
     * @return bool
     */
    public function getCanDeleteAttribute()
    {
        return optional(auth()->user())->can('destroy', $this) ?? false;
    }
}
