<?php

namespace App\Traits;

use App\Scopes\VisibleToUser;

trait HasVisibilityScope
{
    /**
     * Apply visibility scope to all queries
     *
     * @return void
     */
    public static function bootHasVisibilityScope()
    {
        static::addGlobalScope(new VisibleToUser());
    }
}
