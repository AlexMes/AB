<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class SearchMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Builder::macro('searchIn', function ($attributes, $needle) {
            if (is_null($needle)) {
                return $this;
            }

            return $this->where(function (Builder $query) use ($attributes, $needle) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->orWhere($attribute, 'ILIKE', "%{$needle}%");
                }
            });
        });
    }
}
